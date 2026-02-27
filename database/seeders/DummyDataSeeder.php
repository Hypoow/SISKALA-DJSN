<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\ActivityFollowup;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Activity Types & Names
        $activityTemplates = [
            ['internal', 'Rapat Koordinasi Internal'],
            ['internal', 'Rapat Pleno Dewan'],
            ['internal', 'Rapat Komisi PME'],
            ['internal', 'Rapat Komjakum'],
            ['external', 'Audiensi dengan Kementerian Kesehatan'],
            ['external', 'FGD Jaminan Sosial Nasional'],
            ['external', 'Kunjungan Kerja ke BPJS'],
            ['external', 'Undangan Rapat Terbatas Kemenko PMK'],
            ['internal', 'Rapat Persiapan RKAT'],
            ['external', 'Seminar Nasional Sistem Jaminan Sosial'],
        ];

        $locations = [
            'Ruang Rapat Utama DJSN',
            'Ruang Rapat Kecil',
            'Hotel Borobudur Jakarta',
            'Hotel Morrissey',
            'Kantor BPJS Kesehatan',
            'Gedung DPR RI',
            'Zoom Meeting',
        ];

        // Create 35 Activities
        for ($i = 0; $i < 35; $i++) {
            $template = $faker->randomElement($activityTemplates);
            $type = $template[0];
            $baseName = $template[1];
            
            // Random date in past 3 months
            $date = Carbon::today()->subDays(rand(1, 90));
            
            $activity = Activity::create([
                'type' => $type,
                'name' => $baseName . ' - ' . $faker->words(3, true),
                'start_date' => $date->format('Y-m-d'),
                'end_date' => $date->format('Y-m-d'),
                'start_time' => $faker->time('H:i', '17:00'),
                'end_time' => Carbon::parse($faker->time('H:i', '17:00'))->addHours(2)->format('H:i'),
                'status' => 0, // On Schedule
                'invitation_status' => $type == 'external' ? 3 : 1, // Attended / Signed
                'invitation_type' => $type == 'external' ? 'inbound' : 'outbound',
                'location_type' => $faker->randomElement(['offline', 'online', 'hybrid']),
                'location' => $faker->randomElement($locations),
                'organizer_name' => $type == 'external' ? 'Pihak Eksternal' : null,
                'summary_content' => '<ul><li>' . implode('</li><li>', $faker->sentences(rand(3, 5))) . '</li></ul>',
                'pic' => $type == 'internal' ? ['Sekretariat DJSN'] : ['Ketua DJSN'],
                'disposition_to' => $faker->randomElements(['Anggota Dewan Lainnya', 'Komisi PME', 'Sekretariat DJSN'], rand(1, 3)),
                'attendance_list' => $faker->randomElements(['Imron Rosadi', 'Muttaqien', 'Paulus Agung Pambudhi'], rand(1, 3)),
            ]);

            // Add 1-4 Follow Ups per Activity
            $numFollowups = rand(1, 4);
            for ($j = 0; $j < $numFollowups; $j++) {
                ActivityFollowup::create([
                    'activity_id' => $activity->id,
                    'topic' => $faker->sentence(3),
                    'instruction' => $faker->paragraph(1),
                    'pic' => $faker->randomElement(['Sekretariat DJSN', 'Komisi PME', 'Komjakum', 'Bagian Umum']),
                    'status' => $faker->randomElement([0, 1, 2]), // Pending, Progress, Done
                    'deadline' => $date->copy()->addDays(rand(7, 30)),
                    'progress_notes' => $faker->sentence(),
                    'percentage' => $faker->randomElement([0, 25, 50, 75, 100]),
                ]);
            }
        }
    }
}
