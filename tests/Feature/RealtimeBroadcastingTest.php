<?php

namespace Tests\Feature;

use App\Events\RealtimeSyncBroadcasted;
use App\Models\Activity;
use App\Models\ActivityFollowup;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RealtimeBroadcastingTest extends TestCase
{
    use DatabaseMigrations;

    public function test_activity_creation_dispatches_realtime_sync_to_authorized_users(): void
    {
        $user = $this->createSuperAdmin();

        Event::fake([RealtimeSyncBroadcasted::class]);

        $this->actingAs($user);

        $activity = $this->createActivity([
            'name' => 'Rapat Implementasi Reverb',
        ]);

        Event::assertDispatchedTimes(RealtimeSyncBroadcasted::class, 1);
        Event::assertDispatched(RealtimeSyncBroadcasted::class, function (RealtimeSyncBroadcasted $event) use ($user, $activity): bool {
            return in_array($user->id, $event->userIds, true)
                && ($event->payload['scope'] ?? null) === 'activity'
                && ($event->payload['action'] ?? null) === 'created'
                && ($event->payload['entity']['id'] ?? null) === $activity->id
                && ($event->payload['actor_id'] ?? null) === $user->id
                && ($event->payload['topics'] ?? []) === ['activities', 'notifications', 'dashboard'];
        });
    }

    public function test_google_calendar_only_update_does_not_dispatch_realtime_sync(): void
    {
        $user = $this->createSuperAdmin();
        $activity = $this->createActivity();

        Event::fake([RealtimeSyncBroadcasted::class]);

        $this->actingAs($user);

        $activity->update([
            'google_event_id' => 'google-event-123',
        ]);

        Event::assertNotDispatched(RealtimeSyncBroadcasted::class);
    }

    public function test_followup_creation_dispatches_realtime_sync_for_related_activity(): void
    {
        $user = $this->createSuperAdmin();
        $activity = $this->createActivity();

        Event::fake([RealtimeSyncBroadcasted::class]);

        $this->actingAs($user);

        $followup = ActivityFollowup::create([
            'activity_id' => $activity->id,
            'topic' => 'Finalisasi bahan rapat',
            'instruction' => 'Lengkapi bahan sebelum rapat dimulai.',
            'pic' => 'Sekretariat DJSN',
            'percentage' => 0,
            'status' => ActivityFollowup::STATUS_PENDING,
            'deadline' => now()->addDay()->toDateString(),
        ]);

        Event::assertDispatchedTimes(RealtimeSyncBroadcasted::class, 1);
        Event::assertDispatched(RealtimeSyncBroadcasted::class, function (RealtimeSyncBroadcasted $event) use ($user, $activity, $followup): bool {
            return in_array($user->id, $event->userIds, true)
                && ($event->payload['scope'] ?? null) === 'followup'
                && ($event->payload['action'] ?? null) === 'created'
                && ($event->payload['entity']['activity_id'] ?? null) === $activity->id
                && ($event->payload['entity']['followup_id'] ?? null) === $followup->id
                && ($event->payload['actor_id'] ?? null) === $user->id
                && ($event->payload['topics'] ?? []) === ['followups', 'followup-dashboard', 'notifications'];
        });
    }

    public function test_followup_creation_dispatches_realtime_sync_to_followup_dashboard_managers(): void
    {
        $user = $this->createSuperAdmin();
        $followupManager = User::factory()->create([
            'name' => 'Staf Persidangan',
            'email' => 'staf-persidangan@example.com',
            'role' => User::ROLE_PERSIDANGAN,
            'divisi' => 'Bagian Persidangan',
        ]);
        $activity = $this->createActivity([
            'disposition_to' => ['Peserta Lain'],
            'pic' => ['Peserta Lain'],
        ]);

        Event::fake([RealtimeSyncBroadcasted::class]);

        $this->actingAs($user);

        ActivityFollowup::create([
            'activity_id' => $activity->id,
            'topic' => 'Koordinasi tindak lanjut',
            'instruction' => 'Pastikan dashboard tindak lanjut menerima event realtime.',
            'pic' => 'Sekretariat DJSN',
            'percentage' => 0,
            'status' => ActivityFollowup::STATUS_PENDING,
            'deadline' => now()->addDay()->toDateString(),
        ]);

        Event::assertDispatched(RealtimeSyncBroadcasted::class, function (RealtimeSyncBroadcasted $event) use ($followupManager): bool {
            return ($event->payload['scope'] ?? null) === 'followup'
                && ($event->payload['topics'] ?? []) === ['followups', 'followup-dashboard', 'notifications']
                && in_array($followupManager->id, $event->userIds, true);
        });
    }

    public function test_activity_update_still_saves_when_realtime_broadcast_fails(): void
    {
        Queue::fake();

        $user = $this->createSuperAdmin();
        $activity = $this->createActivity();

        Broadcast::extend('always_fails', function () {
            return new class extends Broadcaster {
                public function auth($request): mixed
                {
                    return null;
                }

                public function validAuthenticationResponse($request, $result): mixed
                {
                    return $result;
                }

                public function broadcast(array $channels, $event, array $payload = []): void
                {
                    throw new BroadcastException('Realtime server is unavailable.');
                }
            };
        });

        config(['broadcasting.default' => 'always_fails']);
        app(BroadcastManager::class)->forgetDrivers();

        $response = $this->actingAs($user)->put(route('activities.update', $activity), [
            'activity_type' => 'internal',
            'letter_number' => '001/UND/DJSN/IV/2026',
            'name' => 'Kegiatan Tetap Tersimpan',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'status' => Activity::STATUS_ON_SCHEDULE,
            'invitation_status' => Activity::INV_INT_SENT,
            'invitation_type' => 'outbound',
            'location_type' => 'offline',
            'location' => 'Ruang Rapat',
            'disposition_to' => [],
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_DISPOSISI,
        ]);

        $response->assertRedirect(route('activities.show', $activity->id));
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'name' => 'Kegiatan Tetap Tersimpan',
        ]);
    }

    private function createSuperAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super-admin@example.com',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);
    }

    private function createActivity(array $overrides = []): Activity
    {
        return Activity::create(array_merge([
            'type' => 'internal',
            'letter_number' => '001/UND/DJSN/IV/2026',
            'name' => 'Kegiatan Uji Realtime',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'pic' => ['Sekretariat DJSN'],
            'status' => Activity::STATUS_ON_SCHEDULE,
            'invitation_status' => Activity::INV_INT_SENT,
            'invitation_type' => 'outbound',
            'location_type' => 'offline',
            'location' => 'Ruang Rapat',
            'disposition_to' => [],
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_DISPOSISI,
            'include_tenaga_ahli' => false,
        ], $overrides));
    }
}
