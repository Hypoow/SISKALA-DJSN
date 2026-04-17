<?php

namespace Tests\Feature;

use App\Livewire\ActivityList;
use App\Livewire\PastActivityList;
use App\Models\Activity;
use App\Models\User;
use Tests\Concerns\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class ActivityBulkSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-04-14 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_upcoming_activity_select_all_only_selects_current_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);

        foreach (range(1, 19) as $index) {
            $this->createActivity([
                'name' => "Kegiatan Mendatang {$index}",
                'start_date' => now()->addDays($index + 3)->toDateString(),
            ]);
        }

        $expectedIds = Activity::query()
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->forPage(1, 10)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();

        Livewire::actingAs($user)
            ->test(ActivityList::class)
            ->set('selectAll', true)
            ->assertSet('selected', $expectedIds)
            ->assertSet('selectAll', true);

        $this->assertCount(10, $expectedIds);
    }

    public function test_past_activity_select_all_only_selects_current_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);

        foreach (range(1, 13) as $index) {
            $this->createActivity([
                'name' => "Kegiatan Selesai {$index}",
                'start_date' => now()->subDays($index)->toDateString(),
            ]);
        }

        $expectedIds = Activity::query()
            ->where('start_date', '<', now()->startOfDay())
            ->whereYear('start_date', now()->year)
            ->whereMonth('start_date', now()->month)
            ->orderBy('start_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->forPage(1, 10)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();

        Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->set('selectAll', true)
            ->assertSet('selected', $expectedIds)
            ->assertSet('selectAll', true);

        $this->assertCount(10, $expectedIds);
    }

    private function createActivity(array $overrides = []): Activity
    {
        $defaults = [
            'type' => 'internal',
            'name' => 'Kegiatan Uji',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'pic' => ['Sekretariat DJSN'],
            'status' => Activity::STATUS_ON_SCHEDULE,
            'invitation_status' => Activity::INV_INT_SENT,
            'invitation_type' => 'inbound',
            'location_type' => 'offline',
            'location' => 'Ruang Rapat',
            'disposition_to' => [],
        ];

        return Activity::create(array_merge($defaults, $overrides));
    }
}
