<?php

namespace Tests\Feature;

use App\Jobs\SyncGoogleCalendarEvent;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ActivityTimeIntervalValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_store_rejects_start_time_outside_five_minute_interval(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->post(route('activities.store'), $this->validPayload([
            'start_time' => '09:03',
        ]));

        $response->assertSessionHasErrors('start_time');
        $this->assertDatabaseMissing('activities', [
            'letter_number' => '001/UND/DJSN/IV/2026',
            'start_time' => '09:03:00',
        ]);
    }

    public function test_update_rejects_end_time_outside_five_minute_interval(): void
    {
        $user = $this->createAdminUser();
        $activity = $this->createActivity();

        $response = $this->actingAs($user)->put(route('activities.update', $activity), $this->validPayload([
            'name' => 'Kegiatan Revisi',
            'end_time' => '10:07',
        ]));

        $response->assertSessionHasErrors('end_time');
        $this->assertSame('Kegiatan Uji', $activity->fresh()->name);
    }

    public function test_store_accepts_five_minute_interval_times(): void
    {
        Queue::fake();

        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->post(route('activities.store'), $this->validPayload([
            'start_time' => '09:05',
            'end_time' => '10:35',
        ]));

        $response->assertRedirect(route('activities.index'));
        $this->assertDatabaseHas('activities', [
            'name' => 'Kegiatan Uji',
            'start_time' => '09:05:00',
            'end_time' => '10:35:00',
        ]);
        Queue::assertPushed(SyncGoogleCalendarEvent::class);
    }

    public function test_create_page_renders_time_interval_hint_and_attachment_view_trigger(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->get(route('activities.create'));

        $response->assertOk();
        $response->assertSee('attachment_preview_container', false);
        $response->assertSee('start_time_hour', false);
        $response->assertSee('start_time_minute', false);
        $response->assertSee('>05<', false);
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
        ], $overrides));
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'activity_type' => 'internal',
            'letter_number' => '001/UND/DJSN/IV/2026',
            'name' => 'Kegiatan Uji',
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
        ], $overrides);
    }
}
