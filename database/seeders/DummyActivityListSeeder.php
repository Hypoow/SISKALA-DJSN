<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\ActivityFollowup;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DummyActivityListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Internal PICs
        $pics = ['Sekretariat DJSN', 'Komisi PME', 'Komjakum', 'Ketua DJSN'];
        
        // 1. Upcoming Activities (List Kegiatan) - 5 items
        for ($i = 0; $i < 5; $i++) {
            $startDate = Carbon::now()->addDays(rand(1, 14));
            Activity::create([
                'name' => 'Rapat Koordinasi ' . $faker->sentence(3),
                'start_date' => $startDate,
                'end_date' => $startDate,
                'start_time' => '09:00',
                'end_time' => '12:00',
                'type' => 'internal',
                'pic' => [$faker->randomElement($pics)],
                'status' => 0, // Scheduled
                'location_type' => 'offline',
                'location' => 'Ruang Rapat DJSN',
                // 'description' => $faker->paragraph, // REMOVED: No such column
                'summary_content' => $faker->paragraph, // Using summary_content instead if needed, though usually empty for upcoming
                'disposition_to' => ['Imron Rosadi', 'Muttaqien'], 
            ]);
        }

        // 2. Completed Activities (Kegiatan Selesai) - 5 items
        for ($i = 0; $i < 5; $i++) {
            $startDate = Carbon::now()->subDays(rand(1, 30));
            Activity::create([
                'name' => 'Evaluasi Kinerja ' . $faker->sentence(3),
                'start_date' => $startDate,
                'end_date' => $startDate,
                'start_time' => '13:00',
                'end_time' => '15:00',
                'type' => 'internal',
                'pic' => [$faker->randomElement($pics)],
                'status' => 0, // Done/Terlaksana because it's in the past
                'location_type' => 'online',
                'meeting_link' => 'https://zoom.us/j/dummy',
                // 'description' => $faker->paragraph, // REMOVED
                'summary_content' => '<p>Kegiatan telah dilaksanakan dengan lancar.</p><p>' . $faker->paragraph . '</p>',
                'disposition_to' => ['Imron Rosadi'],
            ]);
        }

        // 3. Activities with Follow Up (Tindak Lanjut) - 5 items
        for ($i = 0; $i < 5; $i++) {
            $startDate = Carbon::now()->subDays(rand(5, 45));
            $activity = Activity::create([
                'name' => 'FGD ' . $faker->sentence(3),
                'start_date' => $startDate,
                'end_date' => $startDate,
                'start_time' => '08:00',
                'end_time' => '16:00',
                'type' => 'external',
                'pic' => [$faker->randomElement($pics)],
                'status' => 0, // Done
                'location_type' => 'offline',
                'location' => 'Hotel ' . $faker->city,
                // 'description' => $faker->paragraph, // REMOVED
                'summary_content' => '<p>Perlu tindak lanjut segera.</p>',
                'disposition_to' => ['Muttaqien', 'Paulus Agung Pambudhi'],
            ]);

            // Add Follow Ups
            for ($j = 0; $j < rand(1, 3); $j++) {
                ActivityFollowup::create([
                    'activity_id' => $activity->id,
                    'topic' => 'Tindak Lanjut ' . $j,
                    'instruction' => $faker->sentence,
                    'pic' => $faker->name,
                    'status' => $faker->randomElement([0, 1]), // Pending or On Progress primarily
                    'deadline' => Carbon::now()->addDays(rand(7, 30)),
                    'percentage' => rand(0, 80),
                ]);
            }
        }
    }
}
