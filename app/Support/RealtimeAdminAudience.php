<?php

namespace App\Support;

use App\Models\User;

class RealtimeAdminAudience
{
    /**
     * @return array<int, int>
     */
    public static function userIds(): array
    {
        return User::query()
            ->with(['division', 'position'])
            ->get()
            ->filter(static fn (User $user): bool => $user->canAccessAdminArea())
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}
