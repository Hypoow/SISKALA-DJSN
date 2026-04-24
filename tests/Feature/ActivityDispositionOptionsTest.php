<?php

namespace Tests\Feature;

use App\Jobs\SyncGoogleCalendarEvent;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ActivityDispositionOptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_store_persists_secretary_status_and_tenaga_ahli_flag(): void
    {
        Queue::fake();

        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->post(route('activities.store'), $this->validPayload([
            'name' => 'Kegiatan Dengan Mengetahui',
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_MENGETAHUI,
            'include_tenaga_ahli' => '1',
        ]));

        $response->assertRedirect(route('activities.index'));
        $this->assertDatabaseHas('activities', [
            'name' => 'Kegiatan Dengan Mengetahui',
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_MENGETAHUI,
            'include_tenaga_ahli' => 1,
        ]);
        Queue::assertPushed(SyncGoogleCalendarEvent::class);
    }

    public function test_update_can_switch_secretary_status_and_clear_tenaga_ahli_flag(): void
    {
        Queue::fake();

        $user = $this->createAdminUser();
        $activity = $this->createActivity([
            'name' => 'Kegiatan Awal',
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_MENGETAHUI,
            'include_tenaga_ahli' => true,
        ]);

        $response = $this->actingAs($user)->put(route('activities.update', $activity), $this->validPayload([
            'name' => 'Kegiatan Revisi',
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_DISPOSISI,
        ]));

        $response->assertRedirect(route('activities.show', $activity->id));
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'name' => 'Kegiatan Revisi',
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_DISPOSISI,
            'include_tenaga_ahli' => 0,
        ]);
        Queue::assertPushed(SyncGoogleCalendarEvent::class);
    }

    public function test_create_page_renders_secretary_status_and_tenaga_ahli_controls(): void
    {
        $user = $this->createAdminUser();
        $this->createSecretaryDispositionUser();

        $response = $this->actingAs($user)->get(route('activities.create'));

        $response->assertOk();
        $response->assertSee('Status Disposisi Sekretaris DJSN');
        $response->assertSee('Mengetahui');
        $response->assertSee('Tenaga Ahli');
        $response->assertSee('secretary_disposition_status', false);
        $response->assertSee('include_tenaga_ahli', false);
        $response->assertSeeInOrder([
            'Sekretaris DJSN',
            'Status Disposisi Sekretaris DJSN',
            'Tenaga Ahli',
        ]);
    }

    public function test_edit_page_renders_secretary_status_and_tenaga_ahli_in_disposition_section(): void
    {
        $user = $this->createAdminUser();
        $this->createSecretaryDispositionUser();
        $activity = $this->createActivity([
            'disposition_to' => [],
        ]);

        $response = $this->actingAs($user)->get(route('activities.edit', $activity));

        $response->assertOk();
        $response->assertSee('Status Disposisi Sekretaris DJSN');
        $response->assertSee('Tenaga Ahli');
        $response->assertSeeInOrder([
            'Sekretaris DJSN',
            'Status Disposisi Sekretaris DJSN',
            'Tenaga Ahli',
        ]);
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
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_DISPOSISI,
            'include_tenaga_ahli' => false,
        ], $overrides));
    }

    private function createSecretaryDispositionUser(): User
    {
        return User::factory()->create([
            'name' => 'Sekretaris DJSN',
            'email' => 'sekretaris@example.com',
            'role' => User::ROLE_SECRETARIAT,
            'divisi' => 'Sekretaris DJSN',
        ]);
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
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_DISPOSISI,
        ], $overrides);
    }
}
