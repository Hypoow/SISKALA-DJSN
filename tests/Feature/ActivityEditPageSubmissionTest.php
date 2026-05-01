<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ActivityEditPageSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_does_not_mark_optional_upload_titles_as_required(): void
    {
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);

        $activity = Activity::create([
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
        ]);

        $response = $this->actingAs($user)->get(route('activities.edit', $activity));

        $response->assertOk();
        $response->assertSee('id="mom_title"', false);
        $response->assertSee('id="material_title"', false);
        $response->assertDontSee('id="mom_title" class="form-control form-control-sm mb-2" placeholder="Judul MoM" required', false);
        $response->assertDontSee('id="material_title" class="form-control form-control-sm mb-2" placeholder="Judul Materi" required', false);
    }
}
