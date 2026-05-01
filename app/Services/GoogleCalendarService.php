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
        $hasSekretariat = self::hasSekretariatAudience($activity, $sekretariatUsers);
        
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
                self::buildUndisposedDescription($activity),
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
            self::applyDefaultReminders($event);
            
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
        if ($activity->type === 'internal') {
            $desc .= self::buildInternalDewanDescriptionBody($activity, $invitedDewan);
            return $desc;
        }

        if (self::usesExternalInvitationTemplate($activity)) {
            return self::buildExternalDewanDescription($activity, $invitedDewan);
        }

        if ($activity->invitation_type == 'inbound') {
            // --- TEMPLATE 1 (External) ---
            // List Names Directly
            foreach ($invitedDewan as $member) {
                $desc .= self::resolveExternalDewanCalendarLabel($member) . "\n";
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
                 $desc .= "{$counter}. {$member->name}\n";
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

    private static function usesExternalInvitationTemplate(Activity $activity): bool
    {
        return $activity->type === 'external' || $activity->invitation_type === 'inbound';
    }

    private static function buildExternalDewanDescription(Activity $activity, $invitedDewan): string
    {
        return self::buildInvitationMemoDescription(
            $activity,
            self::resolveExternalDewanRecipientLines($invitedDewan),
            self::buildExternalInvitationIntro($activity)
        );
    }

    private static function buildExternalSekretariatDescription(Activity $activity): string
    {
        $status = Activity::normalizeSecretaryDispositionStatus($activity->secretary_disposition_status);
        $targetLabel = $status === Activity::SECRETARY_DISPOSITION_STATUS_MENGETAHUI
            ? self::resolveExternalSekretariatMengetahuiTargetLabel($activity)
            : 'Sekretaris DJSN';

        return self::buildInvitationMemoDescription(
            $activity,
            ['Bapak Sekretaris DJSN'],
            self::buildExternalInvitationIntro($activity),
            $targetLabel
        );
    }

    private static function buildUndisposedDescription(Activity $activity): string
    {
        return self::buildInvitationMemoDescription(
            $activity,
            ['Plh. Ketua DJSN'],
            self::buildExternalInvitationIntro($activity),
            'Plh. Ketua DJSN'
        );
    }

    private static function buildInvitationMemoDescription(
        Activity $activity,
        array $recipientLines,
        string $intro,
        ?string $targetLabel = null
    ): string {
        $lines = ['Yth.'];
        $lines = array_merge($lines, self::normalizeTextList($recipientLines));
        $lines[] = '';
        $lines[] = $intro;
        $lines[] = '';
        $lines[] = 'Hari, tanggal : ' . self::formatInvitationDate($activity);
        $lines[] = 'Waktu : ' . self::formatInvitationTime($activity);
        $lines[] = self::formatInvitationLocationLine($activity);

        $onlineMeetingLines = self::formatInvitationOnlineMeetingLines($activity);
        if (!empty($onlineMeetingLines)) {
            $lines[] = '';
            $lines = array_merge($lines, $onlineMeetingLines);
        }

        $targetLabel = trim((string) $targetLabel);
        if ($targetLabel !== '') {
            $lines[] = '';
            $lines[] = 'Kegiatan ditujukan untuk : ' . $targetLabel;
        }

        $lines[] = '';
        $lines[] = 'Demikian disampaikan, terima kasih.';

        return implode("\n", $lines);
    }

    private static function buildExternalInvitationIntro(Activity $activity): string
    {
        $organizer = trim((string) $activity->organizer_name);
        if ($organizer === '') {
            $organizer = 'Pihak Penyelenggara';
        }

        $agenda = trim((string) $activity->name);
        if ($agenda === '') {
            $agenda = 'kegiatan';
        }

        return "Mohon izin menyampaikan Undangan dari {$organizer} terkait {$agenda}, yang akan diselenggarakan pada:";
    }

    private static function formatInvitationDate(Activity $activity): string
    {
        $startDate = Carbon::parse($activity->start_date);
        $endDate = $activity->end_date ? Carbon::parse($activity->end_date) : null;

        if (!$endDate || $startDate->isSameDay($endDate)) {
            return $startDate->translatedFormat('l, j F Y');
        }

        if ($startDate->isSameMonth($endDate) && $startDate->isSameYear($endDate)) {
            return $startDate->translatedFormat('l') . '-' . $endDate->translatedFormat('l')
                . ', ' . $startDate->format('j') . '-' . $endDate->format('j') . ' '
                . $endDate->translatedFormat('F Y');
        }

        return $startDate->translatedFormat('l, j F Y') . ' - ' . $endDate->translatedFormat('l, j F Y');
    }

    private static function formatInvitationTime(Activity $activity): string
    {
        if (!$activity->start_time) {
            return 'rundown terlampir';
        }

        $startTime = Carbon::parse($activity->start_time)->format('H.i');

        if (!$activity->end_time) {
            return "{$startTime} WIB s.d. Selesai";
        }

        $endTime = Carbon::parse($activity->end_time)->format('H.i');

        return "{$startTime} - {$endTime} WIB";
    }

    private static function formatInvitationLocationLine(Activity $activity): string
    {
        if ($activity->location_type === 'online') {
            return 'Media : ' . self::formatOnlineMediaLabel($activity);
        }

        $location = trim((string) $activity->location);

        return 'Tempat : ' . ($location !== '' ? $location : '-');
    }

    private static function formatInvitationOnlineMeetingLines(Activity $activity): array
    {
        if ($activity->location_type === 'offline') {
            return [];
        }

        $lines = [];
        $hasMeetingDetails = $activity->meeting_link || $activity->meeting_id || $activity->passcode;

        if ($hasMeetingDetails) {
            $lines[] = self::formatOnlineMeetingLabel($activity);
        }

        if ($activity->meeting_link) {
            $lines[] = trim((string) $activity->meeting_link);
        }

        if ($activity->meeting_id) {
            $lines[] = 'Meeting ID : ' . trim((string) $activity->meeting_id);
        }

        if ($activity->passcode) {
            $lines[] = 'Passcode : ' . trim((string) $activity->passcode);
        }

        return $lines;
    }

    private static function formatOnlineMediaLabel(Activity $activity): string
    {
        $media = trim((string) $activity->media_online);

        if ($media === '' || str_contains(strtolower($media), 'zoom')) {
            return 'Zoom Meeting';
        }

        return $media;
    }

    private static function resolveExternalDewanRecipientLines($invitedDewan): array
    {
        if ($invitedDewan->isNotEmpty() && self::isAllDewanSelected($invitedDewan)) {
            return ['Seluruh Anggota DJSN'];
        }

        $labels = $invitedDewan
            ->map(fn (User $user) => self::resolveExternalDewanCalendarLabel($user))
            ->filter()
            ->values()
            ->all();

        return empty($labels) ? ['Anggota DJSN'] : $labels;
    }

    private static function resolveExternalSekretariatMengetahuiTargetLabel(Activity $activity): string
    {
        $dewanLabels = self::resolveSelectedDewan($activity)
            ->map(fn (User $user) => self::resolveExternalDewanCalendarLabel($user))
            ->filter()
            ->values()
            ->all();

        if (!empty($dewanLabels)) {
            return self::formatIndonesianList($dewanLabels);
        }

        return 'Sekretaris DJSN';
    }

    private static function resolveSelectedDewan(Activity $activity)
    {
        $disposition = $activity->disposition_to ?? [];
        if (!is_array($disposition) || empty($disposition)) {
            return collect();
        }

        return User::with(['division', 'position'])
            ->whereIn('name', $disposition)
            ->orderBy('order')
            ->get()
            ->filter(fn (User $user) => $user->isDewan())
            ->values();
    }

    private static function isAllDewanSelected($selectedDewan): bool
    {
        $totalDewan = User::with(['division', 'position'])
            ->orderBy('order')
            ->get()
            ->filter(fn (User $user) => $user->isDewan())
            ->count();

        return $totalDewan > 0 && $selectedDewan->count() >= $totalDewan;
    }

    private static function normalizeTextList(array $items): array
    {
        return array_values(array_filter(array_map(
            static fn ($item) => trim((string) $item),
            $items
        )));
    }

    private static function formatIndonesianList(array $items): string
    {
        $items = array_values(array_unique(self::normalizeTextList($items)));
        $count = count($items);

        if ($count === 0) {
            return '-';
        }

        if ($count === 1) {
            return $items[0];
        }

        if ($count === 2) {
            return $items[0] . ' dan ' . $items[1];
        }

        return implode(', ', array_slice($items, 0, -1)) . ', dan ' . $items[$count - 1];
    }

    private static function buildInternalDewanDescriptionBody(Activity $activity, $invitedDewan): string
    {
        $lines = ['A. Anggota Dewan Jaminan Sosial Nasional'];

        foreach ($invitedDewan as $index => $member) {
            $lines[] = ($index + 1) . ". {$member->name}";
        }

        $audienceSections = [];

        if (self::hasInternalDewanSekretariatSection($activity)) {
            $audienceSections[] = "B. Sekretariat DJSN";
        }

        if ((bool) $activity->include_tenaga_ahli) {
            $audienceSections[] = "C. Tenaga Ahli DJSN";
        }

        if (!empty($audienceSections)) {
            $lines[] = '';
            $lines = array_merge($lines, $audienceSections);
        }

        $dateStr = Carbon::parse($activity->start_date)->translatedFormat('l, d F Y');
        $startTime = Carbon::parse($activity->start_time)->format('H.i');
        $endTime = $activity->end_time
            ? Carbon::parse($activity->end_time)->format('H.i') . ' WIB'
            : 'Selesai';

        $lines[] = '';
        $lines[] = "Disampaikan dengan hormat, kami mengundang Bapak/Ibu dalam rapat yang akan diselenggarakan pada:";
        $lines[] = '';
        $lines[] = self::formatInternalDewanDescriptionRow('Hari, Tanggal', $dateStr);
        $lines[] = self::formatInternalDewanDescriptionRow('Waktu', "{$startTime} s.d. {$endTime}");
        $lines[] = self::formatInternalDewanDescriptionRow('Agenda', $activity->name);
        $lines[] = self::formatInternalDewanDescriptionRow('Tempat', self::formatInternalDewanLocation($activity));

        if ($activity->location_type !== 'offline') {
            if ($activity->meeting_link) {
                $lines[] = self::formatInternalDewanDescriptionRow('Link Meeting', $activity->meeting_link);
            }
            if ($activity->meeting_id) {
                $lines[] = self::formatInternalDewanDescriptionRow('Meeting ID', $activity->meeting_id);
            }
            if ($activity->passcode) {
                $lines[] = self::formatInternalDewanDescriptionRow('Passcode', $activity->passcode);
            }
        }

        $lines[] = '';
        $lines[] = "Mengingat pentingnya acara ini, diharapkan Bapak/Ibu dapat hadir pada Rapat tersebut.";
        $lines[] = "Demikian disampaikan, atas perhatian Bapak/Ibu kami ucapkan terima kasih.";

        return implode("\n", $lines);
    }

    private static function formatInternalDewanDescriptionRow(string $label, ?string $value): string
    {
        return str_pad($label, 13) . ' : ' . trim((string) $value);
    }

    private static function hasInternalDewanSekretariatSection(Activity $activity): bool
    {
        $disposition = $activity->disposition_to ?? [];
        if (!is_array($disposition) || empty($disposition)) {
            return false;
        }

        $selectedUsers = User::with(['division', 'position'])
            ->whereIn('name', $disposition)
            ->get()
            ->keyBy('name');

        foreach ($disposition as $name) {
            $user = $selectedUsers->get($name);

            if ($user) {
                if (!$user->isDewan() && !$user->isTA()) {
                    return true;
                }

                continue;
            }

            $group = Activity::normalizeInternalPicLabel($name);
            if (in_array($group, ['Sekretaris DJSN', 'Sekretariat DJSN'], true)) {
                return true;
            }
        }

        return false;
    }

    private static function formatInternalDewanLocation(Activity $activity): string
    {
        $location = trim((string) $activity->location);

        if ($activity->location_type === 'offline') {
            return $location !== '' ? $location : '-';
        }

        $onlineLabel = self::formatOnlineMeetingLabel($activity);

        if ($activity->location_type === 'hybrid' && $location !== '') {
            return trim($location . ' ' . $onlineLabel);
        }

        return $onlineLabel;
    }

    private static function formatOnlineMeetingLabel(Activity $activity): string
    {
        $media = trim((string) $activity->media_online);

        if ($media === '' || strcasecmp($media, 'Zoom') === 0 || str_contains(strtolower($media), 'zoom')) {
            return 'Join Zoom Meeting';
        }

        return $media;
    }

    private static function buildSekretariatDescription(Activity $activity)
    {
        if (self::usesInternalSekretariatDisposisiTemplate($activity)) {
            return self::buildInternalSekretariatDisposisiDescription($activity);
        }

        if (self::usesExternalInvitationTemplate($activity)) {
            return self::buildExternalSekretariatDescription($activity);
        }

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

        $targetLabels = self::resolveSecretariatTargetLabels($activity);
        if (!empty($targetLabels)) {
            $desc .= "\nKegiatan ditujukan untuk: {$activity->secretary_disposition_status_label}\n";
            $desc .= 'Rincian penerima: ' . implode(', ', $targetLabels) . "\n";
        }
        
        $desc .= "\nDemikian disampaikan, atas perhatian Bapak kami ucapkan terima kasih.";
        
        return $desc;
    }

    private static function usesInternalSekretariatDisposisiTemplate(Activity $activity): bool
    {
        return $activity->type === 'internal'
            && $activity->hasDispositionRecipients()
            && Activity::normalizeSecretaryDispositionStatus($activity->secretary_disposition_status) === Activity::SECRETARY_DISPOSITION_STATUS_DISPOSISI;
    }

    private static function buildInternalSekretariatDisposisiDescription(Activity $activity): string
    {
        $agenda = trim((string) $activity->name);
        if ($agenda === '') {
            $agenda = 'kegiatan';
        }

        $lines = [
            'Yth.',
            'Bapak Sekretaris DJSN',
            '',
            "Mohon izin menyampaikan Undangan Rapat terkait {$agenda}, yang akan diselenggarakan pada:",
            '',
            'Hari, tanggal : ' . self::formatInvitationDate($activity),
            'Waktu : ' . self::formatSekretariatTimeRange($activity),
            'Tempat : ' . self::formatSekretariatLocation($activity),
        ];

        $onlineMeetingLines = self::formatSekretariatOnlineMeetingLines($activity);
        if (!empty($onlineMeetingLines)) {
            $lines[] = '';
            $lines = array_merge($lines, $onlineMeetingLines);
        }

        $lines[] = '';
        $lines[] = 'Kegiatan ditujukan untuk : ' . self::resolveInternalSekretariatDisposisiTargetLabel($activity);
        $lines[] = '';
        $lines[] = 'Demikian disampaikan, terima kasih.';

        return implode("\n", $lines);
    }

    private static function formatSekretariatTimeRange(Activity $activity): string
    {
        $startTime = Carbon::parse($activity->start_time)->format('H.i');

        if (!$activity->end_time) {
            return "{$startTime} WIB s.d. Selesai";
        }

        $endTime = Carbon::parse($activity->end_time)->format('H.i');

        return "{$startTime} - {$endTime} WIB";
    }

    private static function formatSekretariatLocation(Activity $activity): string
    {
        $location = trim((string) $activity->location);

        if ($activity->location_type === 'online') {
            return self::formatOnlineMediaLabel($activity);
        }

        return $location !== '' ? $location : '-';
    }

    private static function formatSekretariatOnlineMeetingLines(Activity $activity): array
    {
        if ($activity->location_type === 'offline') {
            return [];
        }

        $lines = [];
        $hasMeetingDetails = $activity->meeting_link || $activity->meeting_id || $activity->passcode;

        if ($hasMeetingDetails) {
            $lines[] = self::formatOnlineMeetingLabel($activity);
        }

        if ($activity->meeting_link) {
            $lines[] = trim((string) $activity->meeting_link);
        }

        if ($activity->meeting_id) {
            $lines[] = 'Meeting ID : ' . trim((string) $activity->meeting_id);
        }

        if ($activity->passcode) {
            $lines[] = 'Passcode : ' . trim((string) $activity->passcode);
        }

        return $lines;
    }

    private static function resolveInternalSekretariatDisposisiTargetLabel(Activity $activity): string
    {
        $disposition = $activity->disposition_to ?? [];
        if (!is_array($disposition) || empty($disposition)) {
            return '-';
        }

        $selectedUsers = User::with(['division', 'position'])
            ->whereIn('name', $disposition)
            ->orderBy('order')
            ->get()
            ->keyBy('name');

        $selectedDewan = collect($disposition)
            ->map(fn ($name) => $selectedUsers->get($name))
            ->filter(fn ($user) => $user instanceof User && $user->isDewan())
            ->values();

        $labels = [];
        $hasSekretariat = false;

        if ($selectedDewan->isNotEmpty()) {
            if (self::isAllDewanSelected($selectedDewan)) {
                $labels[] = 'Seluruh Anggota DJSN';
            } else {
                $labels = array_merge($labels, Activity::sortInternalPicGroups(
                    $selectedDewan
                        ->map(fn (User $user) => Activity::resolveInternalPicGroupForUser($user))
                        ->filter()
                        ->all()
                ));
            }
        }

        foreach ($disposition as $name) {
            $user = $selectedUsers->get($name);

            if ($user instanceof User) {
                if (!$user->isDewan() && !$user->isTA()) {
                    $hasSekretariat = true;
                    break;
                }

                continue;
            }

            $group = Activity::normalizeInternalPicLabel($name);
            if (in_array($group, ['Sekretaris DJSN', 'Sekretariat DJSN'], true)) {
                $hasSekretariat = true;
                break;
            }
        }

        if ($hasSekretariat) {
            $labels[] = 'Tim Sekretariat DJSN';
        }

        if ((bool) $activity->include_tenaga_ahli) {
            $labels[] = 'TA DJSN';
        }

        if (empty($labels)) {
            $labels = self::resolveSecretariatTargetLabels($activity);
        }

        $labels = array_values(array_unique(array_filter($labels)));

        return self::formatIndonesianList($labels);
    }

    private static function hasSekretariatAudience(Activity $activity, $sekretariatUsers): bool
    {
        return $sekretariatUsers->count() > 0 || (bool) $activity->include_tenaga_ahli;
    }

    private static function resolveExternalDewanCalendarLabel(User $user): string
    {
        $label = trim((string) ($user->resolved_report_target_label ?? ''));
        $name = trim((string) $user->name);

        if ($label !== '' && $label !== $name) {
            return $label;
        }

        return $label !== '' ? $label : $name;
    }

    private static function resolveSecretariatTargetLabels(Activity $activity): array
    {
        $disposition = $activity->disposition_to ?? [];
        if (!is_array($disposition)) {
            $disposition = [];
        }

        $users = empty($disposition)
            ? collect()
            : User::with(['division', 'position'])
                ->whereIn('name', $disposition)
                ->orderBy('order')
                ->get()
                ->keyBy('name');

        $labels = [];

        foreach ($disposition as $name) {
            $user = $users->get($name);

            if ($user && $user->isDewan()) {
                continue;
            }

            $label = trim((string) (
                $user?->disposition_secretariat_label
                ?? $user?->resolved_report_target_label
                ?? $name
            ));

            if ($label !== '' && !in_array($label, $labels, true)) {
                $labels[] = $label;
            }
        }

        if ($activity->include_tenaga_ahli && !in_array('Tenaga Ahli', $labels, true)) {
            $labels[] = 'Tenaga Ahli';
        }

        return $labels;
    }

    private static function applyDefaultReminders(\Google_Service_Calendar_Event $event): void
    {
        $reminders = new \Google_Service_Calendar_EventReminders();
        $reminders->setUseDefault(false);

        $overrides = [];
        foreach ([30, 120] as $minutes) {
            $override = new \Google_Service_Calendar_EventReminder();
            $override->setMethod('popup');
            $override->setMinutes($minutes);
            $overrides[] = $override;
        }

        $reminders->setOverrides($overrides);
        $event->setReminders($reminders);
    }
}
