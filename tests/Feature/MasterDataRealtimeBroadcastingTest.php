<?php

namespace Tests\Feature;

use App\Events\RealtimeSyncBroadcasted;
use App\Models\Division;
use App\Models\Position;
use App\Models\Staff;
use App\Models\User;
use Tests\Concerns\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MasterDataRealtimeBroadcastingTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_creation_dispatches_realtime_sync(): void
    {
        $admin = $this->createSuperAdmin();

        Event::fake([RealtimeSyncBroadcasted::class]);

        $response = $this->actingAs($admin)->post(route('master-data.store'), [
            'name' => 'Admin Operasional',
            'email' => 'admin.operasional@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('master-data.index'));
        Event::assertDispatched(RealtimeSyncBroadcasted::class, function (RealtimeSyncBroadcasted $event) use ($admin): bool {
            return in_array($admin->id, $event->userIds, true)
                && ($event->payload['scope'] ?? null) === 'master-data'
                && ($event->payload['action'] ?? null) === 'created'
                && ($event->payload['topics'] ?? []) === ['master-data', 'accounts']
                && ($event->payload['entity']['type'] ?? null) === 'user';
        });
    }

    public function test_staff_creation_dispatches_realtime_sync(): void
    {
        $admin = $this->createSuperAdmin();

        Event::fake([RealtimeSyncBroadcasted::class]);

        $response = $this->actingAs($admin)->post(route('master-data.staff.store'), [
            'name' => 'Staf Pendamping Baru',
            'type' => 'ta',
        ]);

        $response->assertRedirect(route('master-data.staff.index'));
        Event::assertDispatched(RealtimeSyncBroadcasted::class, function (RealtimeSyncBroadcasted $event) use ($admin): bool {
            return in_array($admin->id, $event->userIds, true)
                && ($event->payload['scope'] ?? null) === 'master-data'
                && ($event->payload['topics'] ?? []) === ['master-data', 'staff']
                && ($event->payload['entity']['type'] ?? null) === 'staff';
        });
    }

    public function test_position_creation_dispatches_structure_realtime_sync(): void
    {
        $admin = $this->createSuperAdmin();

        Event::fake([RealtimeSyncBroadcasted::class]);

        $response = $this->actingAs($admin)->post(route('master-data.positions.store'), [
            'name' => 'Jabatan Uji Realtime',
            'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
            'access_profile' => User::ACCESS_PROFILE_VIEWER,
            'order' => 7,
            'receives_disposition' => '1',
        ]);

        $response->assertRedirect();
        Event::assertDispatched(RealtimeSyncBroadcasted::class, function (RealtimeSyncBroadcasted $event) use ($admin): bool {
            return in_array($admin->id, $event->userIds, true)
                && ($event->payload['scope'] ?? null) === 'master-data'
                && ($event->payload['topics'] ?? []) === ['master-data', 'structure']
                && ($event->payload['entity']['type'] ?? null) === 'position';
        });
    }

    public function test_division_reorder_dispatches_structure_realtime_sync(): void
    {
        $admin = $this->createSuperAdmin();
        $divisionA = $this->createDivision('Komisi A', Division::STRUCTURE_GROUP_DEWAN, true, 'komisi_a', 1);
        $divisionB = $this->createDivision('Komisi B', Division::STRUCTURE_GROUP_DEWAN, true, 'komisi_b', 2);

        Event::fake([RealtimeSyncBroadcasted::class]);

        $response = $this->actingAs($admin)->post(route('master-data.divisions.reorder'), [
            'order' => [
                ['id' => $divisionB->id, 'structure_group' => Division::STRUCTURE_GROUP_DEWAN],
                ['id' => $divisionA->id, 'structure_group' => Division::STRUCTURE_GROUP_DEWAN],
            ],
        ]);

        $response->assertOk();
        Event::assertDispatched(RealtimeSyncBroadcasted::class, function (RealtimeSyncBroadcasted $event) use ($admin, $divisionA, $divisionB): bool {
            $ids = $event->payload['entity']['ids'] ?? [];

            sort($ids);
            $expectedIds = [$divisionA->id, $divisionB->id];
            sort($expectedIds);

            return in_array($admin->id, $event->userIds, true)
                && ($event->payload['scope'] ?? null) === 'master-data'
                && ($event->payload['action'] ?? null) === 'reordered'
                && ($event->payload['topics'] ?? []) === ['master-data', 'structure']
                && ($event->payload['entity']['type'] ?? null) === 'divisions'
                && $ids === $expectedIds;
        });
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

    private function createDivision(
        string $name,
        string $structureGroup,
        bool $isCommission = false,
        ?string $commissionCode = null,
        int $order = 0
    ): Division {
        return Division::create([
            'name' => $name,
            'category' => Division::legacyCategoryFor($structureGroup, $isCommission, $name),
            'structure_group' => $structureGroup,
            'access_profile' => User::ACCESS_PROFILE_DEWAN,
            'commission_code' => $commissionCode,
            'is_commission' => $isCommission,
            'order' => $order,
        ]);
    }
}
