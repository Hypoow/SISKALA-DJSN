<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Collection;

class RealtimeAudience
{
    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    public static function forActivity(Activity $activity): Collection
    {
        $users = User::query()
            ->with(['division', 'position'])
            ->get();

        $dispositionRecipients = array_values(array_filter(
            is_array($activity->disposition_to) ? $activity->disposition_to : []
        ));

        $picRecipients = is_array($activity->pic)
            ? array_values(array_filter($activity->pic))
            : array_values(array_filter([(string) $activity->pic]));

        $commissionRecipients = self::buildCommissionRecipientMap($users);

        return $users
            ->filter(function (User $user) use (
                $activity,
                $commissionRecipients,
                $dispositionRecipients,
                $picRecipients
            ): bool {
                if ($user->canViewAllActivities()) {
                    return true;
                }

                if (
                    in_array($user->name, $dispositionRecipients, true)
                    || in_array($user->name, $picRecipients, true)
                ) {
                    return true;
                }

                if (
                    $activity->type === 'internal'
                    && !$activity->hasDispositionRecipients()
                    && $user->canViewInternalActivitiesWithoutDisposition()
                ) {
                    return true;
                }

                if ($activity->shouldNotifyDewanLeadsForUndisposed() && $user->canViewUndisposedExternalActivities()) {
                    return true;
                }

                if (!($user->isTA() || $user->isPersidangan())) {
                    return false;
                }

                foreach ($user->getCommissionKeys() as $commissionKey) {
                    $matchingDewan = $commissionRecipients[$commissionKey] ?? [];

                    if (!empty(array_intersect($dispositionRecipients, $matchingDewan))) {
                        return true;
                    }
                }

                return false;
            })
            ->values();
    }

    /**
     * @return array<int, int>
     */
    public static function userIdsForActivity(Activity $activity): array
    {
        return self::userIds(self::forActivity($activity));
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    public static function forFollowup(Activity $activity): Collection
    {
        return self::forActivity($activity)
            ->concat(self::followupManagers())
            ->unique(static fn (User $user): int => (int) $user->getKey())
            ->values();
    }

    /**
     * @return array<int, int>
     */
    public static function userIdsForFollowup(Activity $activity): array
    {
        return self::userIds(self::forFollowup($activity));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\User>  $users
     * @return array<string, array<int, string>>
     */
    private static function buildCommissionRecipientMap(Collection $users): array
    {
        $map = [];

        foreach ($users as $user) {
            if (!$user->isDewan()) {
                continue;
            }

            foreach ($user->getCommissionKeys() as $commissionKey) {
                $map[$commissionKey][] = $user->name;
            }
        }

        return array_map(
            static fn (array $names) => array_values(array_unique($names)),
            $map
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    private static function followupManagers(): Collection
    {
        return User::query()
            ->with(['division', 'position'])
            ->get()
            ->filter(static fn (User $user): bool => $user->canManageFollowUp())
            ->values();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\User>  $users
     * @return array<int, int>
     */
    private static function userIds(Collection $users): array
    {
        return $users
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}
