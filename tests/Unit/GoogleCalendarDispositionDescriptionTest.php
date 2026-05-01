<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Google_Service_Calendar_Event;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class GoogleCalendarDispositionDescriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_sekretaris_disposisi_description_uses_secretary_target(): void
    {
        $activity = new Activity([
            'type' => 'external',
            'name' => 'Rapat Pengisian dan Pemeriksaan Bukti Dukung EPSS 2026',
            'organizer_name' => 'Kementerian PANRB',
            'invitation_type' => 'inbound',
            'start_date' => '2026-04-30',
            'start_time' => '09:00:00',
            'location_type' => 'offline',
            'location' => 'Ruang Rapat Lt. 11 Selatan B, Grand Kebon Sirih',
            'disposition_to' => ['Sekretaris DJSN'],
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_DISPOSISI,
        ]);

        $description = $this->invokePrivateMethod('buildSekretariatDescription', [$activity]);

        $this->assertSame(
            "Yth.\n"
            . "Bapak Sekretaris DJSN\n\n"
            . "Mohon izin menyampaikan Undangan dari Kementerian PANRB terkait Rapat Pengisian dan Pemeriksaan Bukti Dukung EPSS 2026, yang akan diselenggarakan pada:\n\n"
            . "Hari, tanggal : Kamis, 30 April 2026\n"
            . "Waktu : 09.00 WIB s.d. Selesai\n"
            . "Tempat : Ruang Rapat Lt. 11 Selatan B, Grand Kebon Sirih\n\n"
            . "Kegiatan ditujukan untuk : Sekretaris DJSN\n\n"
            . "Demikian disampaikan, terima kasih.",
            $description
        );

        $this->assertStringNotContainsString('Rincian penerima', $description);
    }

    public function test_external_sekretaris_mengetahui_description_uses_dewan_target_list(): void
    {
        User::factory()->create([
            'name' => 'Mickael',
            'role' => User::ROLE_DEWAN,
            'order' => 10,
        ]);
        User::factory()->create([
            'name' => 'Muttaqien',
            'role' => User::ROLE_DEWAN,
            'order' => 20,
        ]);
        User::factory()->create([
            'name' => 'Agus',
            'role' => User::ROLE_DEWAN,
            'order' => 30,
        ]);

        $activity = new Activity([
            'type' => 'external',
            'name' => 'Rapat Permohonan Tim Verifikasi, Validasi dan Pengecekan Kesiapan Rumah Sakit Uji Coba di Muara Enim',
            'organizer_name' => 'Kementerian Kesehatan',
            'invitation_type' => 'inbound',
            'start_date' => '2026-04-28',
            'end_date' => '2026-04-30',
            'start_time' => '08:30:00',
            'end_time' => '15:00:00',
            'location_type' => 'offline',
            'location' => 'Rumah Sakit di Muara Enim',
            'disposition_to' => ['Sekretaris DJSN', 'Mickael', 'Muttaqien', 'Agus'],
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_MENGETAHUI,
        ]);

        $description = $this->invokePrivateMethod('buildSekretariatDescription', [$activity]);

        $this->assertStringContainsString(
            "Hari, tanggal : Selasa-Kamis, 28-30 April 2026\n"
            . "Waktu : 08.30 - 15.00 WIB\n"
            . "Tempat : Rumah Sakit di Muara Enim",
            $description
        );
        $this->assertStringContainsString(
            'Kegiatan ditujukan untuk : Mickael, Muttaqien, dan Agus',
            $description
        );
        $this->assertStringContainsString('Demikian disampaikan, terima kasih.', $description);
    }

    public function test_internal_sekretaris_disposisi_description_uses_requested_template(): void
    {
        User::factory()->create([
            'name' => 'Sekretaris DJSN',
            'role' => User::ROLE_SECRETARIAT,
            'divisi' => 'Sekretaris DJSN',
            'order' => 1,
        ]);
        User::factory()->create([
            'name' => 'Sudarto',
            'role' => User::ROLE_DEWAN,
            'order' => 10,
        ]);
        User::factory()->create([
            'name' => 'Robben Rico',
            'role' => User::ROLE_DEWAN,
            'order' => 20,
        ]);

        $activity = new Activity([
            'type' => 'internal',
            'name' => 'Pembahasan Penyelesaian Kasus PT. Muara Tunggal',
            'invitation_type' => 'outbound',
            'start_date' => '2026-03-02',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'location_type' => 'hybrid',
            'location' => 'Ruang Rapat DJSN Lt. 15, Grand Kebon Sirih',
            'media_online' => 'Zoom',
            'meeting_link' => 'https://us02web.zoom.us/j/82077639839?pwd=pX0cMmXrEfTklKCAaw3N9b2G1iFaDt.1',
            'meeting_id' => '820 7763 9839',
            'passcode' => '145598',
            'disposition_to' => ['Sekretaris DJSN', 'Sudarto', 'Robben Rico'],
            'secretary_disposition_status' => Activity::SECRETARY_DISPOSITION_STATUS_DISPOSISI,
            'include_tenaga_ahli' => true,
        ]);

        $description = $this->invokePrivateMethod('buildSekretariatDescription', [$activity]);

        $this->assertSame(
            "Yth.\n"
            . "Bapak Sekretaris DJSN\n\n"
            . "Mohon izin menyampaikan Undangan Rapat terkait Pembahasan Penyelesaian Kasus PT. Muara Tunggal, yang akan diselenggarakan pada:\n\n"
            . "Hari, tanggal : Senin, 2 Maret 2026\n"
            . "Waktu : 10.00 - 12.00 WIB\n"
            . "Tempat : Ruang Rapat DJSN Lt. 15, Grand Kebon Sirih\n\n"
            . "Join Zoom Meeting\n"
            . "https://us02web.zoom.us/j/82077639839?pwd=pX0cMmXrEfTklKCAaw3N9b2G1iFaDt.1\n"
            . "Meeting ID : 820 7763 9839\n"
            . "Passcode : 145598\n\n"
            . "Kegiatan ditujukan untuk : Seluruh Anggota DJSN, Tim Sekretariat DJSN, dan TA DJSN\n\n"
            . "Demikian disampaikan, terima kasih.",
            $description
        );
    }

    public function test_build_dewan_description_uses_system_label_for_external_invitees(): void
    {
        User::factory()->create([
            'name' => 'Nama Dewan Asli',
            'role' => User::ROLE_DEWAN,
            'report_target_label' => 'Ketua DJSN',
            'order' => 1,
        ]);
        User::factory()->create([
            'name' => 'Dewan Lain',
            'role' => User::ROLE_DEWAN,
            'order' => 2,
        ]);

        $activity = new Activity([
            'type' => 'external',
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
        $this->assertStringNotContainsString('Nama Dewan Asli', $description);
    }

    public function test_external_dewan_description_collapses_when_all_dewan_are_selected(): void
    {
        User::factory()->create([
            'name' => 'Mickael',
            'role' => User::ROLE_DEWAN,
            'order' => 10,
        ]);
        User::factory()->create([
            'name' => 'Muttaqien',
            'role' => User::ROLE_DEWAN,
            'order' => 20,
        ]);

        $activity = new Activity([
            'type' => 'external',
            'name' => 'Pembahasan Hasil Survei Pemahaman dan Kepuasan Pelayanan Peserta BPJS Ketenagakerjaan Tahun 2025',
            'organizer_name' => 'BPJS Ketenagakerjaan',
            'invitation_type' => 'inbound',
            'start_date' => '2026-04-29',
            'start_time' => '09:00:00',
            'location_type' => 'offline',
            'location' => "Hotel Nawana by Alana\nJl. Siliwangi No. 1, Sumur Batu, Bogor, Jawa Barat",
            'disposition_to' => ['Mickael', 'Muttaqien'],
        ]);

        $description = $this->invokePrivateMethod('buildDewanDescription', [$activity]);

        $this->assertStringContainsString("Yth.\nSeluruh Anggota DJSN\n\n", $description);
        $this->assertStringContainsString("Waktu : 09.00 WIB s.d. Selesai", $description);
        $this->assertStringContainsString(
            "Tempat : Hotel Nawana by Alana\nJl. Siliwangi No. 1, Sumur Batu, Bogor, Jawa Barat",
            $description
        );
        $this->assertStringNotContainsString('Mickael', $description);
    }

    public function test_undisposed_description_is_addressed_to_plh_ketua(): void
    {
        $activity = new Activity([
            'type' => 'external',
            'name' => 'Rapat Koordinasi terkait Penguatan Pendanaan Sektor Kesehatan di Daerah untuk Keberlanjutan Program Kesehatan Nasional',
            'organizer_name' => 'Kementerian Kesehatan',
            'invitation_type' => 'inbound',
            'start_date' => '2026-04-30',
            'start_time' => '08:30:00',
            'location_type' => 'online',
            'media_online' => 'Zoom',
            'meeting_link' => 'https://zoom.us/j/97256474856?pwd=bIXWELs9FUbH6lSH0uaRck0gpYVd2J.1',
            'meeting_id' => '972 5647 4856',
            'passcode' => '12345',
        ]);

        $description = $this->invokePrivateMethod('buildUndisposedDescription', [$activity]);

        $this->assertSame(
            "Yth.\n"
            . "Plh. Ketua DJSN\n\n"
            . "Mohon izin menyampaikan Undangan dari Kementerian Kesehatan terkait Rapat Koordinasi terkait Penguatan Pendanaan Sektor Kesehatan di Daerah untuk Keberlanjutan Program Kesehatan Nasional, yang akan diselenggarakan pada:\n\n"
            . "Hari, tanggal : Kamis, 30 April 2026\n"
            . "Waktu : 08.30 WIB s.d. Selesai\n"
            . "Media : Zoom Meeting\n\n"
            . "Join Zoom Meeting\n"
            . "https://zoom.us/j/97256474856?pwd=bIXWELs9FUbH6lSH0uaRck0gpYVd2J.1\n"
            . "Meeting ID : 972 5647 4856\n"
            . "Passcode : 12345\n\n"
            . "Kegiatan ditujukan untuk : Plh. Ketua DJSN\n\n"
            . "Demikian disampaikan, terima kasih.",
            $description
        );
        $this->assertStringNotContainsString('Keterangan: Belum ada dispo', $description);
    }

    public function test_internal_dewan_description_uses_requested_format_and_master_data_order(): void
    {
        User::factory()->create([
            'name' => 'Robben Rico',
            'role' => User::ROLE_DEWAN,
            'order' => 30,
        ]);
        User::factory()->create([
            'name' => 'Sudarto',
            'role' => User::ROLE_DEWAN,
            'order' => 10,
        ]);
        User::factory()->create([
            'name' => 'Kunta Wibawa Dasa Nugraha',
            'role' => User::ROLE_DEWAN,
            'order' => 20,
        ]);

        $activity = new Activity([
            'type' => 'internal',
            'name' => 'Pembahasan Baseline dan Timeline Perhitungan Manfaat, Tarif dan Iuran JKN',
            'invitation_type' => 'outbound',
            'start_date' => '2026-04-15',
            'start_time' => '08:30:00',
            'end_time' => '11:00:00',
            'location_type' => 'hybrid',
            'location' => 'Ruang Rapat DJSN Lt. 15, Grand Kebon Sirih',
            'media_online' => 'Zoom',
            'meeting_link' => 'https://us02web.zoom.us/j/82176518384',
            'meeting_id' => '821 7651 8384',
            'passcode' => 'POKJA1',
            'disposition_to' => ['Robben Rico', 'Sudarto', 'Kunta Wibawa Dasa Nugraha'],
        ]);

        $description = $this->invokePrivateMethod('buildDewanDescription', [$activity]);

        $this->assertStringContainsString(
            "Yth.\nA. Anggota Dewan Jaminan Sosial Nasional\n1. Sudarto\n2. Kunta Wibawa Dasa Nugraha\n3. Robben Rico\n\nDisampaikan dengan hormat",
            $description
        );
        $this->assertStringNotContainsString("\n\n\n", $description);
        $this->assertStringNotContainsString("B. Sekretariat DJSN", $description);
        $this->assertStringNotContainsString("C. Tenaga Ahli DJSN", $description);
        $this->assertStringContainsString(
            "Disampaikan dengan hormat, kami mengundang Bapak/Ibu dalam rapat yang akan diselenggarakan pada:",
            $description
        );
        $this->assertStringContainsString("Hari, Tanggal : Rabu, 15 April 2026", $description);
        $this->assertStringContainsString("Waktu         : 08.30 s.d. 11.00 WIB", $description);
        $this->assertStringContainsString(
            "Agenda        : Pembahasan Baseline dan Timeline Perhitungan Manfaat, Tarif dan Iuran JKN",
            $description
        );
        $this->assertStringContainsString(
            "Tempat        : Ruang Rapat DJSN Lt. 15, Grand Kebon Sirih Join Zoom Meeting",
            $description
        );
        $this->assertStringContainsString("Link Meeting  : https://us02web.zoom.us/j/82176518384", $description);
        $this->assertStringContainsString("Meeting ID    : 821 7651 8384", $description);
        $this->assertStringContainsString("Passcode      : POKJA1", $description);
        $this->assertStringContainsString(
            "Mengingat pentingnya acara ini, diharapkan Bapak/Ibu dapat hadir pada Rapat tersebut.\nDemikian disampaikan, atas perhatian Bapak/Ibu kami ucapkan terima kasih.",
            $description
        );
        $this->assertStringNotContainsString('Bapak Sudarto', $description);
    }

    public function test_internal_dewan_description_adds_secretariat_and_tenaga_ahli_sections_conditionally(): void
    {
        User::factory()->create([
            'name' => 'Sudarto',
            'role' => User::ROLE_DEWAN,
            'order' => 10,
        ]);
        User::factory()->create([
            'name' => 'Sekretaris DJSN',
            'role' => User::ROLE_SECRETARIAT,
            'order' => 20,
        ]);

        $baseActivity = [
            'type' => 'internal',
            'name' => 'Rapat Internal',
            'invitation_type' => 'outbound',
            'start_date' => '2026-04-15',
            'start_time' => '08:30:00',
            'end_time' => '11:00:00',
            'location_type' => 'offline',
            'location' => 'Ruang Rapat DJSN',
        ];

        $secretariatOnly = new Activity($baseActivity + [
            'disposition_to' => ['Sudarto', 'Sekretaris DJSN'],
            'include_tenaga_ahli' => false,
        ]);
        $secretariatDescription = $this->invokePrivateMethod('buildDewanDescription', [$secretariatOnly]);

        $this->assertStringContainsString("B. Sekretariat DJSN", $secretariatDescription);
        $this->assertStringNotContainsString("C. Tenaga Ahli DJSN", $secretariatDescription);

        $tenagaAhliOnly = new Activity($baseActivity + [
            'disposition_to' => ['Sudarto'],
            'include_tenaga_ahli' => true,
        ]);
        $tenagaAhliDescription = $this->invokePrivateMethod('buildDewanDescription', [$tenagaAhliOnly]);

        $this->assertStringNotContainsString("B. Sekretariat DJSN", $tenagaAhliDescription);
        $this->assertStringContainsString("C. Tenaga Ahli DJSN", $tenagaAhliDescription);
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
