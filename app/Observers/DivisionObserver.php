<?php

namespace App\Observers;

use App\Models\Division;
use App\Support\RealtimeBroadcaster;

class DivisionObserver
{
    private const BROADCASTABLE_FIELDS = [
        'name',
        'short_label',
        'category',
        'structure_group',
        'description',
        'access_profile',
        'commission_code',
        'is_commission',
        'order',
    ];

    public function created(Division $division): void
    {
        RealtimeBroadcaster::broadcastStructure('division', $division, 'created');
    }

    public function updated(Division $division): void
    {
        if (!$division->wasChanged(self::BROADCASTABLE_FIELDS)) {
            return;
        }

        RealtimeBroadcaster::broadcastStructure('division', $division, 'updated');
    }

    public function deleted(Division $division): void
    {
        RealtimeBroadcaster::broadcastStructure('division', $division, 'deleted');
    }
}
