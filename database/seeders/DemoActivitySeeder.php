<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use Carbon\Carbon;

class DemoActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define base time (Assuming current context is around Dec 22, 2025)
        // User requested Past Activities > Dec 16.
        
        $activities = [
            // --- PAST ACTIVITIES (Between Dec 16 and Dec 21) ---
            [
                'type' => 'internal',
                'name' => 'Rapat Koordinasi Komisi PME',
                'start_date' => '2025-12-17',
                'end_date' => '2025-12-17',
                'start_time' => '09:00',
                'end_time' => '12:00',
                'pic' => ['Komisi PME'],
                'status' => 0, // On Schedule
                'invitation_status' => 1, // Signed/Done
                'location' => 'Ruang Rapat Utama DJSN',
                'summary_content' => 'Rapat membahas evaluasi tahunan.',
            ],
            [
                'type' => 'external',
                'name' => 'Undangan FGD Kementerian Kesehatan',
                'start_date' => '2025-12-18',
                'end_date' => '2025-12-18',
                'start_time' => '13:00',
                'end_time' => '15:00',
                'pic' => ['Ketua DJSN'],
                'status' => 0,
                'invitation_status' => 3, // Attended
                'location' => 'Hotel Borobudur Jakarta',
                'organizer_name' => 'Kementerian Kesehatan',
                'summary_content' => null,
            ],
            [
                'type' => 'internal',
                'name' => 'Finalisasi Laporan Tahunan',
                'start_date' => '2025-12-19',
                'end_date' => '2025-12-19',
                'start_time' => '10:00',
                'end_time' => '16:00',
                'pic' => ['Sekretariat DJSN'],
                'status' => 0,
                'invitation_status' => 1,
                'location' => 'Ruang Rapat Kecil',
                'summary_content' => 'Laporan telah disahihkan.',
            ],
             [
                'type' => 'external',
                'name' => 'Workshop Jaminan Sosial BPJS',
                'start_date' => '2025-12-20',
                'end_date' => '2025-12-20',
                'start_time' => '08:30',
                'end_time' => '12:00',
                'pic' => ['Komisi Komjakum'],
                'status' => 0,
                'invitation_status' => 1, // Disposed
                'location' => 'Ballroom Hotel Mulia',
                'organizer_name' => 'BPJS Kesehatan',
                'summary_content' => null,
            ],

            // --- UPCOMING ACTIVITIES (From Dec 23 Onwards) ---
            [
                'type' => 'internal',
                'name' => 'Rapat Pleno DJSN Akhir Tahun',
                'start_date' => '2025-12-23',
                'end_date' => '2025-12-23',
                'start_time' => '09:00',
                'end_time' => '15:00',
                'pic' => ['Ketua DJSN'],
                'status' => 0, // On Schedule
                'invitation_status' => 0, // Sent/Process
                'location' => 'Ruang Rapat Utama',
                'summary_content' => null,
            ],
            [
                'type' => 'external',
                'name' => 'Audiensi dengan DPR RI Komisi IX',
                'start_date' => '2025-12-24',
                'end_date' => '2025-12-24',
                'start_time' => '10:00',
                'end_time' => '12:00',
                'pic' => ['Ketua DJSN', 'Komisi Komjakum'],
                'status' => 0,
                'invitation_status' => 0, // Process Dispo
                'location' => 'Gedung DPR RI',
                'organizer_name' => 'DPR RI',
                'summary_content' => null,
            ],
            [
                'type' => 'internal',
                'name' => 'Workshop Penyusunan RKAT 2026',
                'start_date' => '2025-12-27',
                'end_date' => '2025-12-28',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'pic' => ['Sekretariat DJSN'],
                'status' => 0,
                'invitation_status' => 2, // Drafting
                'location' => 'Hotel Grand Mercure Bandung',
                'summary_content' => null,
            ],
             [
                'type' => 'external',
                'name' => 'Undangan Peresmian Gedung Baru BPJS',
                'start_date' => '2026-01-05',
                'end_date' => '2026-01-05',
                'start_time' => '09:00',
                'end_time' => '11:00',
                'pic' => ['Ketua DJSN'],
                'status' => 0,
                'invitation_status' => 1, // Sudah Dispo
                'location' => 'Kantor Pusat BPJS Ketenagakerjaan',
                'organizer_name' => 'BPJS Ketenagakerjaan',
                'summary_content' => null,
            ],
        ];

        foreach ($activities as $data) {
            Activity::create($data);
        }
    }
}
