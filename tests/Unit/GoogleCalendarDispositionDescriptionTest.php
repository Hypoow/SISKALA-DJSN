<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Division;
use App\Models\Position;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Google_Service_Calendar_Event;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class GoogleCalendarDispositionDescriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_build_sekretariat_description_includes_secretary_status_and_tenaga_ahli(): void
    {
        $secretaryDivision = Division::query()->firstOrCreate(
            ['name' => 'Sekretaris DJSN'],
            [
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARY,
                'access_profile' => User::ACCESS_PROFILE_SET_DJSN,
            ]
        );
        $secretaryPosition = Position::query()->firstOrCreate(
            ['code' => 'sekretaris_djsn'],
            [
                'name' => 'Sekretaris DJSN',
                'access_profile' => User::ACCESS_PROFILE_SET_DJSN,
                'receives_disposition' => true,
            ]
        );
        $persidanganDivision = Division::query()->firstOrCreate(
            ['name' => 'Persidangan'],
            [
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
            ]
        );
        $persidanganPosition = Position::query()->firstOrCreate(
            ['code' => 'kabag_persidangan'],
            [
                'name' => 'Plt/Kabag Persidangan',
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
                'receives_disposition' => true,
            ]
        );

        User::factory()->create([
            'name' => 'Sekretaris DJSN',
            'role' => User::ROLE_SECRETARIAT,
            'divisi' => 'Sekretaris DJSN',
            'division_id' => $secretaryDivision->id,
            'position_id' => $secretaryPosition->id,
            'order' => 1,
        ]);
        User::factory()->create([
            'name' => 'Bagus Persidangan',
            'role' => User::ROLE_PERSIDANGAN,
            'divisi' => 'Persidangan',
            'division_id' => $persidanganDivision->id,
            'position_id' => $persidanganPosition->id,
            'order' => 2,
        ]);

        $activity = new Activity([
            'name' => 'Rapat Koordinasi',
            'invitation_type' => 'outbound',
            'start_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'location_type' => 'offline',
            'location' => 'Ruang Rapat',
            'disposition_to' => ['Sekretaris DJSN', 'Bagus Persidangan'],
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_MENGETAHUI,
            'include_tenaga_ahli' => true,
        ]);

        $description = $this->invokePrivateMethod('buildSekretariatDescription', [$activity]);

        $this->assertStringContainsString('Kegiatan ditujukan untuk: Mengetahui', $description);
        $this->assertStringContainsString(
            'Rincian penerima: Sekretaris DJSN, Plt/Kabag Persidangan, Tenaga Ahli',
            $description
        );
    }

    public function test_build_dewan_description_uses_system_label_for_external_invitees(): void
    {
        User::factory()->create([
            'name' => 'Nama Dewan Asli',
            'role' => User::ROLE_DEWAN,
            'report_target_label' => 'Ketua DJSN',
            'prefix' => 'Bapak',
            'order' => 1,
        ]);

        $activity = new Activity([
            'name' => 'Undangan Eksternal',
            'organizer_name' => 'Kementerian X',
            'invitation_type' => 'inbound',
            'start_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'location_type' => 'offline',
            'location' => 'Ruang Utama',
            'disposition_to' => ['Nama Dewan Asli'],
        ]);

        $description = $this->invokePrivateMethod('buildDewanDescription', [$activity]);

        $this->assertStringContainsString("Yth.\nKetua DJSN", $description);
        $this->assertStringNotContainsString('Bapak Nama Dewan Asli', $description);
    }

    public function test_default_google_calendar_reminders_are_30_minutes_and_2_hours(): void
    {
        $event = new Google_Service_Calendar_Event();

        $this->invokePrivateMethod('applyDefaultReminders', [$event]);

        $reminders = $event->getReminders();

        $this->assertFalse($reminders->getUseDefault());
        $this->assertSame([30, 120], array_map(
            static fn ($reminder) => $reminder->getMinutes(),
            $reminders->getOverrides()
        ));
    }

    private function invokePrivateMethod(string $method, array $arguments = [])
    {
        $reflection = new \ReflectionClass(GoogleCalendarService::class);
        $reflectedMethod = $reflection->getMethod($method);
        $reflectedMethod->setAccessible(true);

        return $reflectedMethod->invokeArgs(null, $arguments);
    }
}
