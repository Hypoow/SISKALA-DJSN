<?php

namespace App\Observers;

use App\Models\Staff;
use App\Support\RealtimeBroadcaster;

class StaffObserver
{
    private const BROADCASTABLE_FIELDS = [
        'name',
        'type',
    ];

    public function created(Staff $staff): void
    {
        RealtimeBroadcaster::broadcastStaff($staff, 'created');
    }

    public function updated(Staff $staff): void
    {
        if (!$staff->wasChanged(self::BROADCASTABLE_FIELDS)) {
            return;
        }

        RealtimeBroadcaster::broadcastStaff($staff, 'updated');
    }

    public function deleted(Staff $staff): void
    {
        RealtimeBroadcaster::broadcastStaff($staff, 'deleted');
    }
}
