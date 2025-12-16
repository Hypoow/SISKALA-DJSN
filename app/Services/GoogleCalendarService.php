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
        
        $dewanUsers = User::whereIn('name', $disposition)->where('role', 'Dewan')->get();
        $sekretariatUsers = User::whereIn('name', $disposition)->whereIn('role', ['DJSN', 'Admin'])->get(); 
        // Note: Admin might be Sekrtariat too. Assuming 'DJSN' is the role for staff.
        
        // Logic: 
        // If Dewan selected -> Create/Update Dewan Event
        // If Sekretariat selected -> Create/Update Sekretariat Event
        
        $dewanSuccess = true;
        $sekretariatSuccess = true;

        // --- DEWAN EVENT ---
        if ($dewanUsers->count() > 0) {
            $dewanSuccess = self::manageEvent(
                $service, 
                $calendarId, 
                $activity, 
                'dewan', 
                $dewanUsers->pluck('email')->toArray(),
                self::buildDewanDescription($activity)
            );
        } else {
            // Delete if exists but no longer selected
            if ($activity->google_event_id_dewan) {
                try { $service->events->delete($calendarId, $activity->google_event_id_dewan); } catch(\Exception $e){}
                $activity->update(['google_event_id_dewan' => null]);
            }
        }

        // --- SEKRETARIAT EVENT ---
        // Also trigger if 'Sekretariat DJSN' string is in disposition (legacy/group check)
        // Or if ANY non-Dewan is selected?
        // User implied split based on who is invited.
        $hasSekretariat = $sekretariatUsers->count() > 0;
        
        if ($hasSekretariat) {
            $sekretariatSuccess = self::manageEvent(
                $service, 
                $calendarId, 
                $activity, 
                'sekretariat', 
                $sekretariatUsers->pluck('email')->toArray(),
                self::buildSekretariatDescription($activity)
            );
        } else {
             if ($activity->google_event_id_sekretariat) {
                try { $service->events->delete($calendarId, $activity->google_event_id_sekretariat); } catch(\Exception $e){}
                $activity->update(['google_event_id_sekretariat' => null]);
            }
        }

        return $dewanSuccess && $sekretariatSuccess;
    }

    private static function manageEvent($service, $calendarId, $activity, $type, $attendees, $description) 
    {
        try {
            $eventIdColumn = "google_event_id_{$type}";
            $currentEventId = $activity->$eventIdColumn;
            
            $event = new \Google_Service_Calendar_Event();
            
            // Basic details
            $event->setSummary($activity->name);
            $event->setDescription($description);
            $event->setColorId($type === 'dewan' ? '11' : '9'); // 11=Red(Dewan?), 9=Blue(Sekretariat?) - Just distinguishing. 
            // User didn't specify color, keeping similar logic (Internal=Blue, External=Red). 
            // Maybe Dewan=External-ish (Red), Sekretariat=Internal (Blue).

            // Time
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
            // Note: Service Accounts cannot invite attendees (send emails) without Domain-Wide Delegation.
            // We list them in the description instead.
            // $event->setAttendees($attendeeList);

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
        // 1. Identify Invitees (Dewan)
        $disposition = $activity->disposition_to ?? [];
        if (!is_array($disposition)) $disposition = [];
        
        $invitedDewan = User::whereIn('name', $disposition)->where('role', 'Dewan')->orderBy('order')->get();
        $count = $invitedDewan->count();

        $desc = "Yth.\n";

        // Logic 1: Header (Who to address)
        if ($count > 5) {
            // Rapat Besar / Pleno -> Use Full Hierarchy with Separated Ketua (But ONLY Invited Ones)
            $invitedKetua = $invitedDewan->where('divisi', 'Ketua DJSN')->first();
            $invitedMembers = $invitedDewan->where('divisi', '!=', 'Ketua DJSN')->sortBy('order');
            
            // A. Ketua
            if ($invitedKetua) {
                $desc .= "A. Ketua Dewan Jaminan Sosial Nasional\n";
                $desc .= "   {$invitedKetua->name}\n\n";
                // Adjust numbering for B if Ketua exists? Template says B. Anggota.
            }
            
            // B. Anggota
            if ($invitedMembers->count() > 0) {
                $desc .= "B. Anggota Dewan Jaminan Sosial Nasional\n";
                $num = 1;
                foreach ($invitedMembers as $member) {
                    $desc .= "{$num}. {$member->name}\n";
                    $num++;
                }
                $desc .= "\n";
            }
            
            $desc .= "C. Sekretariat DJSN\n";
            $desc .= "D. Tenaga Ahli DJSN\n\n";
        } elseif ($count > 0) {
            // Small Meeting -> List Specific Names
            foreach ($invitedDewan as $member) {
                // Formatting name: check if it needs specific honorifics
                // Defaulting to Full Name as it is safest and formal.
                $desc .= "{$member->name}\n";
            }
            $desc .= "\n";
        } else {
             // Fallback if logic called but no dewan found (shouldn't happen)
             $desc .= "Anggota Dewan Jaminan Sosial Nasional\n\n";
        }

        // Logic 2: Opening Sentence (Internal vs External)
        if ($activity->invitation_type == 'inbound') {
            // External / Undangan Masuk
            $desc .= "Mohon izin menyampaikan Undangan terkait {$activity->name}, yang akan diselenggarakan pada:\n\n";
        } else {
            // Internal / Rapat
             $desc .= "Disampaikan dengan hormat, kami mengundang Saudara/i dalam {$activity->name} mengenai agenda tersebut.\n";
             $desc .= "Rapat diselenggarakan pada:\n\n";
        }

        // Logic 3: Details
        $dateStr = Carbon::parse($activity->start_date)->translatedFormat('l, d F Y');
        $timeStr = $activity->start_time . ' WIB s.d. ' . ($activity->end_time ? $activity->end_time . ' WIB' : 'Selesai');
        
        $desc .= "Hari, Tanggal : {$dateStr}\n";
        $desc .= "Waktu       : {$timeStr}\n";
        $desc .= "Tempat      : " . ($activity->location_type == 'offline' ? $activity->location : 'Zoom Meeting') . "\n";
        
        if ($activity->location_type != 'offline') {
            $desc .= "Media       : Zoom Meeting\n";
            $desc .= "Meeting ID  : {$activity->meeting_id}\n";
            $desc .= "Passcode    : {$activity->passcode}\n\n";
            
            $desc .= "Bergabung ke Rapat Zoom\n";
            $desc .= "{$activity->meeting_link}\n\n";
        }

        // Logic 4: Dispo Note / Closing
        if ($activity->dispo_note) {
            $cleanedNote = strip_tags($activity->dispo_note);
            $desc .= "Catatan/Disposisi: {$cleanedNote}\n\n";
        }

        $desc .= "Demikian disampaikan, atas perhatian dan kerja sama Saudara/i kami ucapkan terima kasih.\n\n";
        $desc .= "Salam Hormat";
        
        return $desc;
    }

    private static function buildSekretariatDescription(Activity $activity)
    {
        // Standard Title for Sekretariat Event
        $desc = "Yth.\n";
        $desc .= "Seluruh Sekretariat DJSN\n\n";
        
        // Opening Logic
        if ($activity->invitation_type == 'inbound') {
             $desc .= "Mohon izin menyampaikan undangan perihal {$activity->name}, yang akan diselenggarakan pada:\n\n";
        } else {
             $desc .= "Mengundang Bapak/Ibu dalam {$activity->name}, yang akan diselenggarakan pada:\n\n";
        }
        
        $dateStr = Carbon::parse($activity->start_date)->translatedFormat('l, d F Y');
        $timeStr = $activity->start_time . ' - ' . ($activity->end_time ? $activity->end_time . ' WIB' : 'Selesai');
        
        $desc .= "Hari, tanggal : {$dateStr}\n";
        $desc .= "Waktu : {$timeStr}\n";
        $desc .= "Tempat : " . ($activity->location_type == 'offline' ? $activity->location : 'Zoom Meeting') . "\n";
        
        if ($activity->location_type != 'offline') {
            $desc .= "Link Zoom : {$activity->meeting_link}\n";
            $desc .= "Meeting ID : {$activity->meeting_id}\n";
            $desc .= "Passcode : {$activity->passcode}\n";
        }
        
        if ($activity->dresscode) {
            $desc .= "Pakaian : {$activity->dresscode}\n";
        }
        
        $desc .= "\nDemikian disampaikan, terima kasih.";
        
        return $desc;
    }
}
