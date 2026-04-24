<?php

namespace Tests\Feature;

use App\Livewire\ActivityList;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ActivityListDispositionVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-04-22 09:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dewan_can_see_internal_activity_without_disposition(): void
    {
        $user = User::factory()->create([
            'name' => 'Dewan PME',
            'role' => User::ROLE_DEWAN,
            'divisi' => 'Komisi PME',
        ]);

        $visibleActivity = $this->createActivity([
            'name' => 'Rapat Internal Tanpa Disposisi',
            'disposition_to' => [],
        ]);

        $hiddenActivity = $this->createActivity([
            'name' => 'Undangan Eksternal Tanpa Disposisi',
            'type' => 'external',
            'pic' => [],
            'invitation_status' => Activity::INV_EXT_PROCESS,
            'disposition_to' => [],
        ]);

        Livewire::actingAs($user)
            ->test(ActivityList::class)
            ->assertSee($visibleActivity->name)
            ->assertDontSee($hiddenActivity->name);

        $this->actingAs($user)
            ->get(route('activities.show', $visibleActivity))
            ->assertOk();
    }

    public function test_dewan_does_not_see_activity_disposed_to_other_dewan(): void
    {
        $user = User::factory()->create([
            'name' => 'Dewan PME',
            'role' => User::ROLE_DEWAN,
            'divisi' => 'Komisi PME',
        ]);

        $activity = $this->createActivity([
            'name' => 'Rapat Komisi Lain',
            'disposition_to' => ['Dewan Komjakum'],
        ]);

        Livewire::actingAs($user)
            ->test(ActivityList::class)
            ->assertDontSee($activity->name);

        $this->actingAs($user)
            ->get(route('activities.show', $activity))
            ->assertForbidden();
    }

    public function test_activity_list_can_filter_by_disposition_status(): void
    {
        $user = $this->createAdminUser();

        $disposedActivity = $this->createActivity([
            'name' => 'Kegiatan Sudah Disposisi',
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'disposition_to' => ['Dewan PME'],
        ]);

        $undisposedActivity = $this->createActivity([
            'name' => 'Kegiatan Belum Disposisi',
            'start_date' => now()->addDays(6)->toDateString(),
            'end_date' => now()->addDays(6)->toDateString(),
            'disposition_to' => [],
        ]);

        Livewire::actingAs($user)
            ->test(ActivityList::class)
            ->set('dispositionFilter', 'with_disposition')
            ->assertSee($disposedActivity->name)
            ->assertDontSee($undisposedActivity->name)
            ->set('dispositionFilter', 'without_disposition')
            ->assertSee($undisposedActivity->name)
            ->assertDontSee($disposedActivity->name);
    }

    public function test_index_page_renders_disposition_filter(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->get(route('activities.index'));

        $response->assertOk();
        $response->assertSee('Status Disposisi');
        $response->assertSee('Tanpa Disposisi');
    }

    private function createAdminUser(): User
    {
        return User::factory()->create([
            'name' => 'Super Admin',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);
    }

    private function createActivity(array $overrides = []): Activity
    {
        return Activity::create(array_merge([
            'type' => 'internal',
            'name' => 'Kegiatan Uji',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'pic' => ['Sekretariat DJSN'],
            'status' => Activity::STATUS_ON_SCHEDULE,
            'invitation_status' => Activity::INV_INT_SENT,
            'invitation_type' => 'outbound',
            'location_type' => 'offline',
            'location' => 'Ruang Rapat',
            'disposition_to' => [],
        ], $overrides));
    }
}
