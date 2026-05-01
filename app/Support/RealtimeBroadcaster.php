<?php

namespace App\Support;

use App\Events\RealtimeSyncBroadcasted;
use App\Models\Activity;
use App\Models\ActivityFollowup;
use App\Models\Division;
use App\Models\Position;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class RealtimeBroadcaster
{
    public static function broadcastActivity(Activity $activity, string $action): void
    {
        self::broadcast(
            RealtimeAudience::userIdsForActivity($activity),
            [
                'topics' => ['activities', 'notifications', 'dashboard'],
                'scope' => 'activity',
                'action' => $action,
                'entity' => [
                    'type' => 'activity',
                    'id' => $activity->getKey(),
                    'activity_id' => $activity->getKey(),
                ],
                'message' => self::activityMessage($action),
                'actor_id' => auth()->id(),
                'occurred_at' => now()->toIso8601String(),
            ]
        );
    }

    public static function broadcastActivitySegment(Activity $activity, string $segment, string $action): void
    {
        self::broadcast(
            RealtimeAudience::userIdsForActivity($activity),
            [
                'topics' => ['activities'],
                'scope' => 'activity',
                'action' => $segment . '.' . $action,
                'entity' => [
                    'type' => 'activity',
                    'id' => $activity->getKey(),
                    'activity_id' => $activity->getKey(),
                    'segment' => $segment,
                ],
                'message' => 'Kegiatan diperbarui.',
                'actor_id' => auth()->id(),
                'occurred_at' => now()->toIso8601String(),
            ]
        );
    }

    public static function broadcastFollowup(ActivityFollowup $followup, string $action): void
    {
        $activity = $followup->relationLoaded('activity')
            ? $followup->activity
            : $followup->activity()->first();

        if (!$activity) {
            return;
        }

        self::broadcast(
            RealtimeAudience::userIdsForFollowup($activity),
            [
                'topics' => ['followups', 'followup-dashboard', 'notifications'],
                'scope' => 'followup',
                'action' => $action,
                'entity' => [
                    'type' => 'followup',
                    'id' => $followup->getKey(),
                    'followup_id' => $followup->getKey(),
                    'activity_id' => $activity->getKey(),
                ],
                'message' => self::followupMessage($action),
                'actor_id' => auth()->id(),
                'occurred_at' => now()->toIso8601String(),
            ]
        );
    }

    public static function broadcastAccount(User $user, string $action): void
    {
        self::broadcastAdminArea(
            'accounts',
            $action,
            [
                'type' => 'user',
                'id' => $user->getKey(),
                'user_id' => $user->getKey(),
            ],
            self::adminMessage('Akun', $action)
        );
    }

    public static function broadcastStaff(Staff $staff, string $action): void
    {
        self::broadcastAdminArea(
            'staff',
            $action,
            [
                'type' => 'staff',
                'id' => $staff->getKey(),
                'staff_id' => $staff->getKey(),
            ],
            self::adminMessage('Staf pendamping', $action)
        );
    }

    public static function broadcastStructure(string $resource, Division|Position $model, string $action): void
    {
        self::broadcastAdminArea(
            'structure',
            $action,
            [
                'type' => $resource,
                'id' => $model->getKey(),
                $resource . '_id' => $model->getKey(),
            ],
            self::adminMessage($resource === 'division' ? 'Unit kerja' : 'Jabatan', $action)
        );
    }

    /**
     * @param  array<string, mixed>  $entity
     */
    public static function broadcastAdminArea(string $area, string $action, array $entity = [], ?string $message = null): void
    {
        self::broadcast(
            RealtimeAdminAudience::userIds(),
            [
                'topics' => ['master-data', $area],
                'scope' => 'master-data',
                'action' => $action,
                'entity' => $entity,
                'message' => $message ?? self::adminMessage('Master data', $action),
                'actor_id' => auth()->id(),
                'occurred_at' => now()->toIso8601String(),
            ]
        );
    }

    /**
     * @param  array<int, int>  $userIds
     * @param  array<string, mixed>  $payload
     */
    private static function broadcast(array $userIds, array $payload): void
    {
        $uniqueUserIds = array_values(array_unique(array_filter(
            array_map(static fn ($userId) => (int) $userId, $userIds)
        )));

        if ($uniqueUserIds === []) {
            return;
        }

        $payload['topics'] = array_values(array_unique($payload['topics'] ?? []));

        try {
            RealtimeSyncBroadcasted::dispatch($uniqueUserIds, $payload);
        } catch (Throwable $exception) {
            Log::warning('Realtime broadcast failed; primary data change was kept.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'user_ids' => $uniqueUserIds,
                'scope' => $payload['scope'] ?? null,
                'action' => $payload['action'] ?? null,
                'entity' => $payload['entity'] ?? null,
            ]);
        }
    }

    private static function activityMessage(string $action): string
    {
        return match ($action) {
            'created' => 'Kegiatan baru tersedia.',
            'deleted', 'force-deleted' => 'Kegiatan dihapus.',
            'restored' => 'Kegiatan dipulihkan.',
            default => 'Kegiatan diperbarui.',
        };
    }

    private static function followupMessage(string $action): string
    {
        return match ($action) {
            'created' => 'Tindak lanjut baru tersedia.',
            'deleted' => 'Tindak lanjut dihapus.',
            default => 'Tindak lanjut diperbarui.',
        };
    }

    private static function adminMessage(string $resourceLabel, string $action): string
    {
        return match ($action) {
            'created' => $resourceLabel . ' baru tersedia.',
            'deleted' => $resourceLabel . ' dihapus.',
            'reordered' => $resourceLabel . ' diperbarui.',
            default => $resourceLabel . ' diperbarui.',
        };
    }
}
