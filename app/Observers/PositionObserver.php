<?php

namespace App\Observers;

use App\Models\Position;
use App\Support\RealtimeBroadcaster;

class PositionObserver
{
    private const BROADCASTABLE_FIELDS = [
        'name',
        'code',
        'structure_group',
        'access_profile',
        'order',
        'receives_disposition',
        'disposition_group_label',
        'report_target_label',
    ];

    public function created(Position $position): void
    {
        RealtimeBroadcaster::broadcastStructure('position', $position, 'created');
    }

    public function updated(Position $position): void
    {
        if (!$position->wasChanged(self::BROADCASTABLE_FIELDS)) {
            return;
        }

        RealtimeBroadcaster::broadcastStructure('position', $position, 'updated');
    }

    public function deleted(Position $position): void
    {
        RealtimeBroadcaster::broadcastStructure('position', $position, 'deleted');
    }
}
