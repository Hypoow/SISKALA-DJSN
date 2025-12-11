<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    /**
     * Get a configured Google Client instance.
     */
    private static function getClient()
    {
        $calendarId = config('google-calendar.calendar_id');
        $credentialsPath = config('google-calendar.service_account_credentials_json');

        if (!$credentialsPath || !$calendarId) {
            Log::warning('Google Calendar credentials or Calendar ID not configured.');
            return null;
        }

        try {
            // Force unset environment variables that might trigger delegation
            putenv('GOOGLE_APPLICATION_CREDENTIALS');
            putenv('GOOGLE_ACCOUNT_IMPERSONATE');
            
            $client = new \Google_Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(\Google_Service_Calendar::CALENDAR);
            // EXPLICITLY DO NOT SET SUBJECT to avoid 403 Delegation Error
            
            return $client;
        } catch (\Exception $e) {
            Log::error('Failed to create Google Client: ' . $e->getMessage());
            return null;
        }
    }

    public static function createEvent(Activity $activity)
    {
        $client = self::getClient();
        if (!$client) {
            return false;
        }

        try {
            Log::info('DEBUG: Environment Check', [
                'sapi' => php_sapi_name(),
                'credentials_path' => config('google-calendar.service_account_credentials_json'),
                'env_google_creds' => getenv('GOOGLE_APPLICATION_CREDENTIALS'),
                'client_class' => get_class($client),
            ]);

            $service = new \Google_Service_Calendar($client);
            $calendarId = config('google-calendar.calendar_id');

            // Prepare Dates
            $startDate = \Carbon\Carbon::parse($activity->start_date->format('Y-m-d') . ' ' . $activity->start_time);
            $endDate = null;
            if ($activity->end_date) {
                 if ($activity->end_time) {
                     $endDate = \Carbon\Carbon::parse($activity->end_date->format('Y-m-d') . ' ' . $activity->end_time);
                 } else {
                     // If end date exists but no end time, maybe assume end of day or just same time?
                     // Current logic elsewhere defaults 1 hour if no end time.
                     // Let's rely on the logic below for event object, but for description we can use what we have.
                 }
            }

            // Prepare Description
            $description = self::buildDescription($activity, $startDate, $endDate);

            // Determine Color ID (9 = Blueberry/Blue for Internal, 11 = Tomato/Red for External)
            $colorId = ($activity->type === 'internal') ? '9' : '11';

            $event = new \Google_Service_Calendar_Event([
                'summary' => $activity->name,
                'description' => $description,
                'colorId' => $colorId,
                'start' => [
                    'dateTime' => $startDate->toRfc3339String(),
                    'timeZone' => config('app.timezone'),
                ],
                'end' => [
                    'dateTime' => ($activity->end_date && $activity->end_time && $endDate) ? $endDate->toRfc3339String() : $startDate->copy()->addHour()->toRfc3339String(),
                    'timeZone' => config('app.timezone'),
                ],
            ]);

            // Set Location based on Location Type (Offline vs Online)
            if ($activity->location_type == 'offline') {
                $event->setLocation($activity->location);
            } else {
                // For online, use the meeting link as location
                $event->setLocation($activity->meeting_link);
            }

            $savedEvent = $service->events->insert($calendarId, $event);

            $activity->update(['google_event_id' => $savedEvent->id]);

            Log::info('Google Calendar event created successfully (Manual Client)', [
                'activity_id' => $activity->id,
                'google_event_id' => $savedEvent->id
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to create Google Calendar event: ' . $e->getMessage());
            return false;
        }
    }

    public static function updateEvent(Activity $activity)
    {
        if (!$activity->google_event_id) {
            self::createEvent($activity);
            return;
        }

        $client = self::getClient();
        if (!$client) {
            return;
        }

        try {
            $service = new \Google_Service_Calendar($client);
            $calendarId = config('google-calendar.calendar_id');

            $event = $service->events->get($calendarId, $activity->google_event_id);
            
            // Prepare Dates
            $startDate = \Carbon\Carbon::parse($activity->start_date->format('Y-m-d') . ' ' . $activity->start_time);
            $endDate = null;
            if ($activity->end_date && $activity->end_time) {
                 $endDate = \Carbon\Carbon::parse($activity->end_date->format('Y-m-d') . ' ' . $activity->end_time);
            }

            // Prepare Description
            $description = self::buildDescription($activity, $startDate, $endDate);

            $event->setSummary($activity->name);
            $event->setDescription($description);
            
            // Determine Color ID (9 = Blueberry/Blue for Internal, 11 = Tomato/Red for External)
            // Note: Google Calendar API does not support custom hex codes for events, only preset colorIds.
            // 9 is the closest to #007bff (Blueberry).
            $colorId = ($activity->type === 'internal') ? '9' : '11';
            $event->setColorId($colorId);
            
            $start = new \Google_Service_Calendar_EventDateTime();
            $start->setDateTime($startDate->toRfc3339String());
            $start->setTimeZone(config('app.timezone'));
            $event->setStart($start);

            $end = new \Google_Service_Calendar_EventDateTime();
            $end->setDateTime(($activity->end_date && $activity->end_time && $endDate) ? $endDate->toRfc3339String() : $startDate->copy()->addHour()->toRfc3339String());
            $end->setTimeZone(config('app.timezone'));
            $event->setEnd($end);

            // Set Location based on Location Type (Offline vs Online)
            if ($activity->location_type == 'offline') {
                $event->setLocation($activity->location);
            } else {
                // For online, use the meeting link as location
                $event->setLocation($activity->meeting_link);
            }

            // Clear attendees if any (since we are not using them)
            $event->setAttendees([]);

            $service->events->update($calendarId, $activity->google_event_id, $event);
            Log::info('Google Calendar event updated successfully (Manual Client)');

        } catch (\Exception $e) {
            Log::error('Failed to update Google Calendar event: ' . $e->getMessage());
        }
    }

    public static function deleteEvent(Activity $activity)
    {
        if (!$activity->google_event_id) {
            return;
        }

        $client = self::getClient();
        if (!$client) {
            return;
        }

        try {
            $service = new \Google_Service_Calendar($client);
            $calendarId = config('google-calendar.calendar_id');

            $service->events->delete($calendarId, $activity->google_event_id);
            Log::info('Google Calendar event deleted successfully (Manual Client)');

        } catch (\Exception $e) {
            Log::error('Failed to delete Google Calendar event: ' . $e->getMessage());
        }
    }

    private static function getAttendees(Activity $activity)
    {
        $emails = [];
        
        if ($activity->disposition_to && is_array($activity->disposition_to)) {
            $names = $activity->disposition_to;
            $users = User::whereIn('name', $names)->get();
            foreach ($users as $user) {
                if ($user->email) {
                    $emails[] = $user->email;
                }
            }
        }

        return array_unique($emails);
    }

    private static function buildDescription(Activity $activity, $startDate, $endDate)
    {
        // 1. Process HTML Note: Convert Breaks/Paragraphs to Newlines
        $rawNote = $activity->dispo_note;
        $note = str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $rawNote);
        $note = strip_tags($note); // Strip remaining tags like <b>, <i>, <p>
        $note = trim($note);

        // 2. Check if Note already acts as a Full Description (contains sync headers)
        // We check for "Nama Kegiatan:" or "Waktu:" which are generated by the Sync button
        $hasHeaders = (stripos($note, 'Nama Kegiatan:') !== false) || (stripos($note, 'Waktu:') !== false);

        if ($hasHeaders) {
            // If the user used the Sync button (or typed manual headers), use the note AS IS.
            // Just append invites at the bottom if needed.
            $description = $note;
        } else {
            // If strictly a note (no headers), PREPEND the auto-generated info.
            $description = "Nama Kegiatan: " . $activity->name . "\n";
            $description .= "Waktu: " . $startDate->translatedFormat('l, d F Y H:i') . " s.d. " . ($activity->end_time ? ($endDate ? $endDate->format('H:i') : 'Selesai') : 'Selesai') . "\n";
            $description .= "Lokasi: " . ($activity->location_type == 'offline' ? $activity->location : $activity->meeting_link) . "\n";
            $description .= "Tujuan Disposisi: " . (is_array($activity->disposition_to) ? implode(', ', $activity->disposition_to) : $activity->disposition_to) . "\n";
            
            if (!empty($note)) {
                $description .= "\nKeterangan:\n" . $note . "\n";
            }
        }

        // 3. Append Attendees (Always useful to see who was emailed)
        $attendeeEmails = self::getAttendees($activity);
        if (!empty($attendeeEmails)) {
            $description .= "\n\nUndangan untuk: " . implode(', ', $attendeeEmails);
        }

        return $description;
    }
}
