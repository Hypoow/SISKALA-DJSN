<?php

namespace App\Observers;

use App\Models\ActivityFollowup;
use App\Support\RealtimeBroadcaster;

class ActivityFollowupObserver
{
    private const BROADCASTABLE_FIELDS = [
        'activity_id',
        'topic',
        'instruction',
        'pic',
        'progress_notes',
        'percentage',
        'status',
        'deadline',
        'notes',
        'completion_date',
    ];

    public function created(ActivityFollowup $followup): void
    {
        RealtimeBroadcaster::broadcastFollowup($followup, 'created');
    }

    public function updated(ActivityFollowup $followup): void
    {
        if (!$followup->wasChanged(self::BROADCASTABLE_FIELDS)) {
            return;
        }

        RealtimeBroadcaster::broadcastFollowup($followup, 'updated');
    }

    public function deleted(ActivityFollowup $followup): void
    {
        RealtimeBroadcaster::broadcastFollowup($followup, 'deleted');
    }
}
