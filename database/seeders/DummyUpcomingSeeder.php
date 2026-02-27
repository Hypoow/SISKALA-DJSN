<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DummyUpcomingSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Activity Templates for Upcoming
        $activityTemplates = [
            ['internal', 'Rapat Koordinasi Anggaran'],
            ['internal', 'Rapat Pleno Bulanan'],
            ['external', 'Kunjungan Kerja DPR RI'],
            ['external', 'FGD Reformasi Jaminan Sosial'],
            ['internal', 'Monitoring dan Evaluasi Triwulan'],
            ['external', 'Undangan Rapat Harmonisasi RPP'],
            ['internal', 'Rapat Teknis IT'],
            ['external', 'Simposium Kesehatan Nasional'],
            ['internal', 'Pelatihan SDM Sekretariat'],
            ['external', 'Audiensi dengan Serikat Pekerja'],
        ];

        $locations = [
            'Ruang Rapat Utama DJSN',
            'Ruang Rapat Kecil Dept. Keuangan',
            'Hotel Ibis Jakarta',
            'Hotel Ritz Carlton',
            'Gedung Kemenko PMK',
            'Zoom Meeting',
        ];

        // Create 30 Activities
        for ($i = 0; $i < 30; $i++) {
            $template = $faker->randomElement($activityTemplates);
            $type = $template[0];
            $baseName = $template[1];
            
            // Random date in next 60 days
            $date = Carbon::today()->addDays(rand(1, 60));
            
            Activity::create([
                'type' => $type,
                'name' => $baseName . ' - ' . $faker->words(3, true),
                'start_date' => $date->format('Y-m-d'),
                'end_date' => $date->format('Y-m-d'),
                'start_time' => $faker->time('H:i', '17:00'),
                'end_time' => Carbon::parse($faker->time('H:i', '17:00'))->addHours(2)->format('H:i'),
                'status' => 0, // On Schedule
                'invitation_status' => $type == 'external' ? 0 : 0, // Process / Sent
                'invitation_type' => $type == 'external' ? 'inbound' : 'outbound',
                'location_type' => $faker->randomElement(['offline', 'online', 'hybrid']),
                'location' => $faker->randomElement($locations),
                'organizer_name' => $type == 'external' ? 'Penyelenggara Eksternal' : null,
                'summary_content' => null, // Upcoming usually has no summary yet
                'pic' => $type == 'internal' ? ['Sekretariat DJSN'] : ['Ketua DJSN'],
                'disposition_to' => $faker->randomElements(['Komisi PME', 'Komjakum', 'Sekretariat DJSN'], rand(1, 2)),
                'attendance_list' => [],
            ]);
        }
    }
}
