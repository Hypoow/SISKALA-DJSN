<?php

namespace Tests\Feature;

use App\Livewire\PastActivityList;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class PostActivityAssetRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        Carbon::setTestNow('2026-04-23 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_upload_mom_requires_pdf_file(): void
    {
        Storage::fake('tmp-for-tests');
        Storage::fake('public');

        $user = $this->createSuperAdmin();
        $activity = $this->createActivity();

        $response = $this->actingAs($user)->post(route('activities.upload-mom', $activity), [
            'title' => 'MoM Rapat Evaluasi',
            'file_path' => UploadedFile::fake()->create(
                'mom-rapat.docx',
                128,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ),
        ]);

        $response->assertSessionHasErrors('file_path');
        $this->assertDatabaseCount('activity_moms', 0);
    }

    public function test_upload_documentation_route_allows_incremental_uploads_below_minimum_threshold(): void
    {
        Storage::fake('tmp-for-tests');
        Storage::fake('public');

        $user = $this->createSuperAdmin();
        $activity = $this->createPastActivity();

        $response = $this->actingAs($user)->post(route('activities.upload-documentation', $activity), [
            'file_path' => [
                UploadedFile::fake()->image('dokumentasi-1.jpg'),
            ],
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('success', 'Dokumentasi berhasil ditambahkan. Saat ini baru 1 foto. Lengkapi minimal 4 foto.');
        $this->assertDatabaseHas('activity_documentations', [
            'activity_id' => $activity->id,
        ]);
    }

    public function test_past_activity_mom_upload_enters_edit_mode_after_successful_upload(): void
    {
        Storage::fake('public');

        $user = $this->createSuperAdmin();
        $activity = $this->createPastActivity();

        $uploadedFile = new class
        {
            public function getClientOriginalName(): string
            {
                return 'mom-evaluasi.pdf';
            }

            public function storeAs(string $path, string $name, string $disk): string
            {
                $storedPath = trim($path . '/' . $name, '/');
                Storage::disk($disk)->put($storedPath, '%PDF-1.4 test mom upload');

                return $storedPath;
            }

            public function getRealPath(): string
            {
                return storage_path('app/testing/non-existent-mom-upload.tmp');
            }
        };

        $component = Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->call('openMomModal', $activity->id);

        $component->instance()->newMomTitle = 'MoM Rapat Evaluasi';
        $component->instance()->newMomFile = $uploadedFile;
        $component->instance()->saveMom();

        $mom = $activity->moms()->firstOrFail();

        $this->assertSame($mom->id, $component->instance()->editingMomId);
        $this->assertSame('MoM Rapat Evaluasi', $component->instance()->editingMomTitle);

        $this->assertDatabaseHas('activity_moms', [
            'id' => $mom->id,
            'activity_id' => $activity->id,
            'title' => 'MoM Rapat Evaluasi',
        ]);
    }

    public function test_past_activity_mom_title_can_be_updated_from_modal(): void
    {
        $user = $this->createSuperAdmin();
        $activity = $this->createPastActivity();
        $mom = $activity->moms()->create([
            'title' => 'MoM Draft',
            'file_path' => "activity_moms/{$activity->id}/mom-draft.pdf",
        ]);

        Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->call('openMomModal', $activity->id)
            ->call('startEditingMom', $mom->id)
            ->assertSet('editingMomId', $mom->id)
            ->assertSet('editingMomTitle', 'MoM Draft')
            ->set('editingMomTitle', 'MoM Final Rapat Evaluasi')
            ->call('updateMom')
            ->assertHasNoErrors()
            ->assertSet('editingMomId', null)
            ->assertSee('MoM Final Rapat Evaluasi');

        $this->assertDatabaseHas('activity_moms', [
            'id' => $mom->id,
            'title' => 'MoM Final Rapat Evaluasi',
        ]);
    }

    public function test_past_activity_documentation_modal_displays_updated_limit_hint(): void
    {
        $user = $this->createSuperAdmin();
        $activity = $this->createPastActivity();

        Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->call('openDocumentationModal', $activity->id)
            ->assertSee('Minimal 4, Maks 8 Foto');
    }

    public function test_past_activity_summary_actions_use_clear_button_labels(): void
    {
        $user = $this->createSuperAdmin();

        $this->createPastActivity([
            'name' => 'Rapat Belum Ada Ringkasan',
        ]);

        $this->createPastActivity([
            'name' => 'Rapat Sudah Ada Ringkasan',
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->subDays(2)->toDateString(),
            'summary_content' => '<p>Ringkasan rapat sudah tersedia.</p>',
        ]);

        Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->assertSee('Isi Ringkasan')
            ->assertSee('Lihat Ringkasan')
            ->assertDontSee('Ringkasan Rapat Terisi');
    }

    public function test_past_activity_list_does_not_show_secretariat_as_pic_label(): void
    {
        $user = $this->createSuperAdmin();

        $this->createPastActivity([
            'name' => 'Rapat Dewan Dengan Sekretariat',
            'pic' => ['Komisi PME', 'Sekretaris DJSN', 'Sekretariat DJSN'],
        ]);

        Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->assertSee('Komisi PME')
            ->assertDontSee('Sekretaris DJSN')
            ->assertDontSee('Sekretariat DJSN');
    }

    public function test_past_activity_material_modal_can_mark_activity_as_having_no_materials(): void
    {
        $user = $this->createSuperAdmin();
        $activity = $this->createPastActivity();

        Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->call('openMaterialModal', $activity->id)
            ->call('toggleNoMaterialStatus')
            ->assertSet('hasNoMaterials', true);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'has_no_materials' => 1,
        ]);
    }

    public function test_upload_material_route_renames_file_and_clears_no_material_status(): void
    {
        Storage::fake('tmp-for-tests');
        Storage::fake('public');

        $user = $this->createSuperAdmin();
        $activity = $this->createActivity([
            'name' => 'Undangan blablabla',
            'start_date' => '2026-02-23',
            'end_date' => '2026-02-23',
            'has_no_materials' => true,
        ]);

        $response = $this->actingAs($user)->post(route('activities.upload-material', $activity), [
            'title' => 'Paparan Dewan',
            'file_path' => UploadedFile::fake()->create('paparan.pdf', 128, 'application/pdf'),
        ]);

        $expectedPath = "activity_materials/{$activity->id}/20260223_UndanganBlablabla.pdf";

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'has_no_materials' => 0,
        ]);
        $this->assertDatabaseHas('activity_materials', [
            'activity_id' => $activity->id,
            'title' => 'Paparan Dewan',
            'file_path' => $expectedPath,
        ]);
        Storage::disk('public')->assertExists($expectedPath);
    }

    public function test_legacy_persidangan_user_can_upload_material(): void
    {
        Storage::fake('tmp-for-tests');
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'Staf Sidang PME',
            'role' => User::ROLE_SECRETARIAT,
            'divisi' => 'Persidangan Komisi PME',
        ]);

        $activity = $this->createActivity([
            'pic' => [$user->name],
        ]);

        $response = $this->actingAs($user)->post(route('activities.upload-material', $activity), [
            'title' => 'Paparan Komisi PME',
            'file_path' => UploadedFile::fake()->create('paparan-pme.pdf', 128, 'application/pdf'),
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('activity_materials', [
            'activity_id' => $activity->id,
            'title' => 'Paparan Komisi PME',
        ]);
    }

    public function test_past_activity_material_upload_uses_activity_date_and_name_for_filename(): void
    {
        Storage::fake('tmp-for-tests');
        Storage::fake('public');

        $user = $this->createSuperAdmin();
        $activity = $this->createPastActivity([
            'name' => 'Undangan blablabla',
            'start_date' => '2026-02-23',
            'end_date' => '2026-02-23',
        ]);

        Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->call('openMaterialModal', $activity->id)
            ->set('newMaterialTitle', 'Paparan Dewan')
            ->set('newMaterialFile', UploadedFile::fake()->create('paparan.pdf', 128, 'application/pdf'))
            ->call('saveMaterial')
            ->assertHasNoErrors();

        $expectedPath = "activity_materials/{$activity->id}/20260223_UndanganBlablabla.pdf";

        $this->assertDatabaseHas('activity_materials', [
            'activity_id' => $activity->id,
            'title' => 'Paparan Dewan',
            'file_path' => $expectedPath,
        ]);
        Storage::disk('public')->assertExists($expectedPath);
    }

    public function test_attendance_modal_places_secretary_section_after_dewan_groups(): void
    {
        $user = $this->createSuperAdmin();
        $activity = $this->createPastActivity();

        User::factory()->create([
            'name' => 'Nikodemus Beriman Purba',
            'email' => 'nikodemus@example.com',
            'role' => User::ROLE_DEWAN,
            'divisi' => 'Komisi PME',
            'disposition_group_label' => 'Komisi PME',
            'order' => 1,
        ]);

        User::factory()->create([
            'name' => 'Imron Rosadi',
            'email' => 'imron@example.com',
            'role' => User::ROLE_SECRETARIAT,
            'divisi' => 'Sekretaris DJSN',
            'disposition_group_label' => 'Sekretaris DJSN',
            'receives_disposition' => true,
            'order' => 2,
        ]);

        $activity->update([
            'disposition_to' => [
                'Nikodemus Beriman Purba',
                'Imron Rosadi',
            ],
        ]);

        Livewire::actingAs($user)
            ->test(PastActivityList::class)
            ->call('openAssignmentModal', $activity->id)
            ->assertSeeInOrder([
                'Komisi PME',
                'Nikodemus Beriman Purba',
                'Sekretaris DJSN',
                'Imron Rosadi',
                'Diwakili',
            ]);
    }

    private function createSuperAdmin(): User
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
            'name' => 'Rapat Evaluasi',
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
        ], $overrides));
    }

    private function createPastActivity(array $overrides = []): Activity
    {
        return $this->createActivity(array_merge([
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
        ], $overrides));
    }
}
