<?php

namespace App\Observers;

use App\Models\Activity;
use App\Support\RealtimeBroadcaster;

class ActivityObserver
{
    private const BROADCASTABLE_FIELDS = [
        'type',
        'letter_number',
        'name',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'pic',
        'narasumber',
        'status',
        'invitation_status',
        'invitation_type',
        'location_type',
        'location',
        'media_online',
        'meeting_link',
        'meeting_id',
        'passcode',
        'dispo_note',
        'secretary_disposition_status',
        'include_tenaga_ahli',
        'disposition_to',
        'dresscode',
        'attachment_path',
        'minutes_path',
        'assignment_letter_path',
        'summary_content',
        'organizer_name',
        'attendance_list',
        'attendance_details',
        'updated_by',
        'has_no_materials',
    ];

    public function created(Activity $activity): void
    {
        RealtimeBroadcaster::broadcastActivity($activity, 'created');
    }

    public function updated(Activity $activity): void
    {
        if (!$activity->wasChanged(self::BROADCASTABLE_FIELDS)) {
            return;
        }

        RealtimeBroadcaster::broadcastActivity($activity, 'updated');
    }

    public function deleted(Activity $activity): void
    {
        RealtimeBroadcaster::broadcastActivity($activity, 'deleted');
    }

    public function restored(Activity $activity): void
    {
        RealtimeBroadcaster::broadcastActivity($activity, 'restored');
    }
}
