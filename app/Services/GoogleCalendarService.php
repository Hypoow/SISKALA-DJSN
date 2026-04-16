<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private static $lastError = null;

    public static function getLastError()
    {
        return self::$lastError;
    }

    private static function getClient()
    {
        self::$lastError = null; // Reset error
        $calendarId = config('google-calendar.calendar_id');
        $credentialsPath = config('google-calendar.service_account_credentials_json');

        if (!$credentialsPath || !$calendarId) {
            self::$lastError = 'Credentials not configured';
            Log::warning('Google Calendar credentials or Calendar ID not configured.');
            return null;
        }

        try {
            putenv('GOOGLE_APPLICATION_CREDENTIALS');
            putenv('GOOGLE_ACCOUNT_IMPERSONATE');
            
            $client = new \Google_Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(\Google_Service_Calendar::CALENDAR);
            $client->addScope(\Google_Service_Drive::DRIVE_FILE); // Add Drive Scope for attachments
            
            return $client;
        } catch (\Exception $e) {
            Log::error('Failed to create Google Client: ' . $e->getMessage());
            return null;
        }
    }

    public static function createEvent(Activity $activity)
    {
        return self::syncEvents($activity);
    }

    public static function updateEvent(Activity $activity)
    {
        return self::syncEvents($activity);
    }

    public static function deleteEvent(Activity $activity)
    {
        $client = self::getClient();
        if (!$client) return;
        $service = new \Google_Service_Calendar($client);
        $calendarId = config('google-calendar.calendar_id');

        // Delete Dewan Event
        if ($activity->google_event_id_dewan) {
            try {
                $service->events->delete($calendarId, $activity->google_event_id_dewan);
            } catch (\Exception $e) {
                Log::error('Failed to delete Dewan Event: ' . $e->getMessage());
            }
        }

        // Delete Sekretariat Event
        if ($activity->google_event_id_sekretariat) {
            try {
                $service->events->delete($calendarId, $activity->google_event_id_sekretariat);
            } catch (\Exception $e) {
                Log::error('Failed to delete Sekretariat Event: ' . $e->getMessage());
            }
        }

        // Delete Legacy Event (if exists)
        if ($activity->google_event_id) {
            try {
                $service->events->delete($calendarId, $activity->google_event_id);
            } catch (\Exception $e) {}
        }
    }

    private static function syncEvents(Activity $activity)
    {
        $client = self::getClient();
        if (!$client) return false;
        
        $service = new \Google_Service_Calendar($client);
        $calendarId = config('google-calendar.calendar_id');

        $disposition = $activity->disposition_to ?? [];
        if (!is_array($disposition)) $disposition = [];

        // Identify Targets
        // Dewan: Role 'Dewan'
        // Sekretariat: Role 'DJSN' or specific division names
        // Ideally we query Users to check roles based on names in disposition
        
        $targetUsers = User::with(['division', 'position'])
            ->whereIn('name', $disposition)
            ->get();

        $dewanUsers = $targetUsers->filter(fn (User $user) => $user->isDewan())->values();
        $sekretariatUsers = $targetUsers->reject(fn (User $user) => $user->isDewan())->values();
        
        // Logic: 
        // If Dewan selected -> Create/Update Dewan Event
        // If Sekretariat selected -> Create/Update Sekretariat Event
        
        $dewanSuccess = true;
        $sekretariatSuccess = true;

        // Also trigger if 'Sekretariat DJSN' string is in disposition (legacy/group check)
        // Or if ANY non-Dewan is selected?
        // User implied split based on who is invited.
        $hasSekretariat = $sekretariatUsers->count() > 0;
        
        // Logic for Undisposed Activities
        if ($dewanUsers->isEmpty() && !$hasSekretariat) {
             // If NO disposition (Undisposed), create a placeholder event in the Sekretariat slot.
             // Color: Red (11)
             // Description: "Keterangan: Belum di dispo"
             
             // Reuse Sekretariat slot
             $undisposedSuccess = self::manageEvent(
                $service, 
                $calendarId, 
                $activity, 
                'sekretariat', 
                [], // No attendees
                self::buildSekretariatDescription($activity) . "\n\nKeterangan: Belum ada dispo",
                '11' // Force Red Color
            );
            
            // Ensure Dewan slot is clear
            if ($activity->google_event_id_dewan) {
                try { $service->events->delete($calendarId, $activity->google_event_id_dewan); } catch(\Exception $e){}
                $activity->update(['google_event_id_dewan' => null]);
            }
            
            return $undisposedSuccess;
        }

        // Standard Logic (Disposed)
        
        // Process Sekretariat First
        if ($hasSekretariat) {
            $sekretariatSuccess = self::manageEvent(
                $service, 
                $calendarId, 
                $activity, 
                'sekretariat', 
                $sekretariatUsers->pluck('email')->toArray(),
                self::buildSekretariatDescription($activity)
                // Color handled internally (8)
            );
        } else {
             if ($activity->google_event_id_sekretariat) {
                try { $service->events->delete($calendarId, $activity->google_event_id_sekretariat); } catch(\Exception $e){}
                $activity->update(['google_event_id_sekretariat' => null]);
            }
        }

        // Process Dewan Last
        if ($dewanUsers->count() > 0) {
            $dewanSuccess = self::manageEvent(
                $service, 
                $calendarId, 
                $activity, 
                'dewan', 
                $dewanUsers->pluck('email')->toArray(),
                self::buildDewanDescription($activity)
                // Color handled internally (9)
            );
        } else {
            if ($activity->google_event_id_dewan) {
                try { $service->events->delete($calendarId, $activity->google_event_id_dewan); } catch(\Exception $e){}
                $activity->update(['google_event_id_dewan' => null]);
            }
        }
        
        return $dewanSuccess && $sekretariatSuccess;
    }

    private static function manageEvent($service, $calendarId, $activity, $type, $attendees, $description, $overrideColorId = null) 
    {
        try {
            $eventIdColumn = "google_event_id_{$type}";
            $currentEventId = $activity->$eventIdColumn;
            
            
            $event = new \Google_Service_Calendar_Event();
            
            // Append suffix removed as per user request
            $event->setSummary($activity->name);
            
            $event->setDescription($description);
            
            // Color Logic
            if ($overrideColorId) {
                $event->setColorId($overrideColorId);
            } else {
                // Dewan: #213f66 ~ Blueberry (9)
                // Sekretariat: #a9a9a9 ~ Graphite (8)
                if ($type === 'dewan') {
                    $event->setColorId('9'); 
                } else {
                    $event->setColorId('8');
                }
            }

            // Time
            // Reverted time offset as per user request ("JANGAN UBAH WAKTU NYA")
            // Times are exact.
            
            $startDate = Carbon::parse($activity->start_date->format('Y-m-d') . ' ' . $activity->start_time);
            
            $endDate = $activity->end_date && $activity->end_time 
                ? Carbon::parse($activity->end_date->format('Y-m-d') . ' ' . $activity->end_time)
                : $startDate->copy()->addHour();

            $start = new \Google_Service_Calendar_EventDateTime();
            $start->setDateTime($startDate->toRfc3339String());
            $start->setTimeZone(config('app.timezone'));
            $event->setStart($start);

            $end = new \Google_Service_Calendar_EventDateTime();
            $end->setDateTime($endDate->toRfc3339String());
            $end->setTimeZone(config('app.timezone'));
            $event->setEnd($end);

            // Location
            if ($activity->location_type == 'offline') {
                $event->setLocation($activity->location);
            } else {
                $event->setLocation($activity->meeting_link);
            }

            // Attendees

            if ($currentEventId) {
                // Update
                $service->events->update($calendarId, $currentEventId, $event);
            } else {
                // Create
                $newEvent = $service->events->insert($calendarId, $event);
                $activity->update([$eventIdColumn => $newEvent->id]);
            }
            
            return true;

        } catch (\Exception $e) {
            self::$lastError = "Failed to manage {$type} event: " . $e->getMessage();
            Log::error(self::$lastError);
            return false;
        }
    }

    private static function buildDewanDescription(Activity $activity)
    {
        // Identify Invitees (Dewan)
        $disposition = $activity->disposition_to ?? [];
        if (!is_array($disposition)) $disposition = [];
        
        $invitedDewan = User::with(['division', 'position'])
            ->whereIn('name', $disposition)
            ->orderBy('order')
            ->get()
            ->filter(fn (User $user) => $user->isDewan())
            ->values();
        $count = $invitedDewan->count();

        $desc = "Yth.\n";

        // === TEMPLATE 1 & 3: Header Logic ===
        if ($activity->invitation_type == 'inbound') {
            // --- TEMPLATE 1 (External) ---
            // List Names Directly
            foreach ($invitedDewan as $member) {
                $salutation = $member->prefix ?? 'Bapak';
                $desc .= "{$salutation} {$member->name}\n";
            }
            $desc .= "\n";
            
            // Body
            $instansi = $activity->organizer_name ? $activity->organizer_name : 'Pihak Penyelenggara';
            $desc .= "Mohon izin menyampaikan Undangan dari {$instansi} terkait {$activity->name}, yang akan diselenggarakan pada:\n\n";

        } else {
            // --- TEMPLATE 3 (Internal) ---
            // Full Hierarchy Header
            // A. Dewan
            $invitedKetua = $invitedDewan->where('divisi', 'Ketua DJSN')->first();
            $invitedMembers = $invitedDewan->where('divisi', '!=', 'Ketua DJSN')->sortBy('order');

            $desc .= "A. Dewan Jaminan Sosial Nasional\n";
            
             // Counter for numbered list
             $counter = 1;
             
             // List all invited dewan sorted by order
             // Note: Re-fetching/Sorting might be needed if Ketua/Members split isn't strictly needed for the list order,
             // but user example implies a single list 1..N.
             // We'll trust $invitedDewan which is already sorted by 'order' from line 226.
             
             foreach ($invitedDewan as $member) {
                 $salutation = $member->prefix ?? 'Bapak';
                 $desc .= "{$counter}. {$salutation} {$member->name}\n";
                 $counter++;
             }
             $desc .= "\n";

            $desc .= "B. Sekretariat Dewan Jaminan Sosial Nasional\n";
            
            $desc .= "Dengan hormat,\n\n";

            // Body
            // Unit Name comes from selected Internal PIC
            $unitName = 'Komisi/Unit';
            if ($activity->pic && count($activity->pic) > 0) {
                $unitName = $activity->pic[0]; // Take the first PIC as the Organizer Unit
            }

            $desc .= "Disampaikan bahwa {$unitName} akan melaksanakan {$activity->name}. Sehubungan dengan hal tersebut, kami mengundang Bapak/Ibu untuk hadir dan berpartisipasi dalam kegiatan dimaksud yang akan dilaksanakan pada:\n\n";
        }

        // === COMMON DETAILS SECTION ===
        $dateStr = Carbon::parse($activity->start_date)->translatedFormat('l, d F Y');
        $startTime = Carbon::parse($activity->start_time)->format('H:i');
        $endTime = $activity->end_time ? Carbon::parse($activity->end_time)->format('H:i') . ' WIB' : 'Selesai';
        $timeStr = $startTime . ' - ' . $endTime;
        
        $desc .= "Hari, tanggal : {$dateStr}\n";
        $desc .= "Waktu : {$timeStr}\n";

        if ($activity->location_type == 'offline') {
            $desc .= "Tempat : {$activity->location}\n";
        } else {
             $desc .= "Media / Tempat : Zoom Meeting {$activity->location}\n";
        }
        
        if ($activity->location_type != 'offline') {
            if ($activity->meeting_link) {
                $desc .= "Link Zoom : {$activity->meeting_link}\n";
            }
            if ($activity->meeting_id) {
                $desc .= "Meeting ID : {$activity->meeting_id}\n";
            }
            if ($activity->passcode) {
                $desc .= "Passcode : {$activity->passcode}\n";
            }
        }
        
        if ($activity->invitation_type != 'inbound') {
             // For Template 3 (Internal), add Agenda placeholder if mostly empty?
             // Or rely on description field if needed.
             // $desc .= "\nAgenda : [Agenda Kegiatan]\n"; 
        }

        $desc .= "\n";
        
        if ($activity->invitation_type == 'inbound') {
             $desc .= "Demikian disampaikan, atas perhatian Bapak/Ibu kami ucapkan terima kasih.";
        } else {
             $desc .= "Demikian disampaikan, atas perhatian dan kerja sama Bapak/Ibu kami ucapkan terima kasih.\n\n";
             $desc .= "Hormat kami,";
        }
        
        return $desc;
    }

    private static function buildSekretariatDescription(Activity $activity)
    {
        $desc = "Yth.\n";
        $desc .= "Bapak Sekretaris DJSN\n\n";
        
        if ($activity->invitation_type == 'inbound') {
            // --- TEMPLATE 2 (External) ---
            $instansi = $activity->organizer_name ? $activity->organizer_name : 'Pihak Penyelenggara';
            $desc .= "Mohon izin menyampaikan Undangan dari {$instansi} terkait {$activity->name}, yang akan diselenggarakan pada:\n\n";
        } else {
            // --- TEMPLATE 4 (Internal) ---
            $desc .= "Mohon izin menyampaikan Undangan terkait {$activity->name}, yang akan diselenggarakan pada:\n\n";
        }
        
        $dateStr = Carbon::parse($activity->start_date)->translatedFormat('l, d F Y');
        $dateStr = Carbon::parse($activity->start_date)->translatedFormat('l, d F Y');
        $startTime = Carbon::parse($activity->start_time)->format('H:i');
        $endTime = $activity->end_time ? Carbon::parse($activity->end_time)->format('H:i') . ' WIB' : 'Selesai';
        $timeStr = $startTime . ' s.d. ' . $endTime;
        
        $desc .= "Hari, tanggal : {$dateStr}\n";
        $desc .= "Waktu : {$timeStr}\n";
        $desc .= "Tempat : " . ($activity->location_type == 'offline' ? $activity->location : 'Zoom Meeting') . "\n";
        
        if ($activity->location_type != 'offline') {
            if ($activity->meeting_link) {
                $desc .= "Link : {$activity->meeting_link}\n";
            }
            if ($activity->meeting_id) {
                $desc .= "ID : {$activity->meeting_id}\n";
            }
            if ($activity->passcode) {
                $desc .= "Pass : {$activity->passcode}\n";
            }
        }

        $desc .= "\nKegiatan ditujukan untuk:\n";
        
        // List Invitees (This logic works for both Template 2 and 4 as per request)
        // We list ALL disposition targets
        $disposition = $activity->disposition_to ?? [];
        if (is_array($disposition) && count($disposition) > 0) {
             // Fetch users to get their titles (divisi) and role
             $users = User::with(['division', 'position'])->whereIn('name', $disposition)->get()->keyBy('name');

             $lines = []; // Initialize an array to hold formatted lines
             $number = 1; // Initialize counter for numbered list

             foreach ($disposition as $name) {
                 $user = $users->get($name);
                 
                 // SKIP if user is Dewan, as requested (focus on Sekretariat)
                 if ($user && $user->isDewan()) {
                     continue;
                 }

                 $label = $name;
                 
                 // Check if user has a title associated with "Kepala" (Head) or is "Sekretaris DJSN"
                 // Usage based on "divisi" field in User model (as populated by SekretariatSeeder)
                 if ($user && !empty($user->divisi)) {
                     $title = $user->divisi;
                     // Logic: If title contains 'Kepala' (e.g. Kepala Bagian, Kepala Sub) or is 'Sekretaris DJSN'
                     // We use the title instead of the name.
                     if (stripos($title, 'Kepala') !== false || stripos($title, 'Sekretaris DJSN') !== false) {
                         $label = $title;
                     }
                 }
                 
                 $desc .= "{$label}\n";
             }
        }
        
        $desc .= "\nDemikian disampaikan, atas perhatian Bapak kami ucapkan terima kasih.";
        
        return $desc;
    }
}
