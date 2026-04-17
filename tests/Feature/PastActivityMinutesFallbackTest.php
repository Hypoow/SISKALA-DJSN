<?php

namespace Tests\Feature;

use App\Livewire\PastActivityList;
use App\Models\Activity;
use App\Models\User;
use Tests\Concerns\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PastActivityMinutesFallbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-04-16 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_assignment_modal_uses_mom_file_when_legacy_minutes_are_empty(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'Super Admin',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);

        $activity = $this->createPastActivity();

        $mom = $activity->moms()->create([
            'title' => 'Notulensi Rapat Monitoring',
            'file_path' => "activity_moms/{$activity->id}/notulensi-monitoring.pdf",
        ]);

        Storage::disk('public')->put($mom->file_path, 'dummy pdf');

        Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->call('openAssignmentModal', $activity->id)
            ->assertSee('Diambil dari MoM')
            ->assertSee('Notulensi Rapat Monitoring')
            ->assertDontSee('Notulensi Kosong');
    }

    public function test_assignment_modal_minutes_upload_updates_legacy_minutes_path(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'Super Admin',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);

        $activity = $this->createPastActivity();

        $file = UploadedFile::fake()->create('notulensi-rapat.pdf', 128, 'application/pdf');

        Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->call('openAssignmentModal', $activity->id)
            ->set('newMinutesFile', $file);

        $activity->refresh();

        $this->assertSame("minutes/{$activity->id}/notulensi-rapat.pdf", $activity->minutes_path);
        Storage::disk('public')->assertExists($activity->minutes_path);
    }

    private function createPastActivity(array $overrides = []): Activity
    {
        $defaults = [
            'type' => 'internal',
            'name' => 'Rapat Evaluasi',
            'start_date' => now()->subDays(3)->toDateString(),
            'end_date' => now()->subDays(3)->toDateString(),
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
