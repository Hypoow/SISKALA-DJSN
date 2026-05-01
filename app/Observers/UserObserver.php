<?php

namespace App\Observers;

use App\Models\User;
use App\Support\RealtimeBroadcaster;

class UserObserver
{
    private const BROADCASTABLE_FIELDS = [
        'name',
        'report_target_label',
        'receives_disposition',
        'disposition_group_label',
        'email',
        'role',
        'divisi',
        'division_id',
        'position_id',
        'order',
    ];

    public function created(User $user): void
    {
        RealtimeBroadcaster::broadcastAccount($user, 'created');
    }

    public function updated(User $user): void
    {
        if (!$user->wasChanged(self::BROADCASTABLE_FIELDS)) {
            return;
        }

        RealtimeBroadcaster::broadcastAccount($user, 'updated');
    }

    public function deleted(User $user): void
    {
        RealtimeBroadcaster::broadcastAccount($user, 'deleted');
    }
}
