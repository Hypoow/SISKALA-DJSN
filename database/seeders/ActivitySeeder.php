<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        $councilMembers = [
            'Prof. Dr. Ir. R. Nunung Nuryartono, M.Si.',
            'Muttaqien, S.S., M.P.H., A.A.K.',
            'Nikodemus Beriman Purba, S.Psi., M.H.',
            'Sudarto, S.E., M.B.A., M.Kom., Ph.D., CGEIT., CA.',
            'Robben Rico, A.Md., LLAJ., S.H., S.T., M.Si.',
            'Dr. dr. Mahesa Paranadipa Maykel, M.H., MARS.',
            'Dr.rer.pol. Syamsul Hidayat Pasaribu, S.E., M.Si.',
            'Hermansyah, S.H., AK3.',
            'Drs. Paulus Agung Pambudhi, M.M.',
            'dr. H. Agus Taufiqurrohman, M.Kes., Sp.S.',
            'Kunta Wibawa Dasa Nugraha, S.E., M.A., Ph.D.',
            'Dra. Indah Anggoro Putri, M.Bus.',
            'Prof. Dr. Rudi Purwono, S.E., M.SE.',
            'Mickael Bobby Hoelman, S.E., M.Si.',
            'Royanto Purba, S.T.'
        ];

        $internalPics = ['Ketua DJSN', 'Komisi PME', 'Komisi Komjakum', 'Sekretariat DJSN'];

        for ($i = 0; $i < 100; $i++) {
            $type = $faker->randomElement(['internal', 'external']);
            $locationType = $faker->randomElement(['online', 'offline']);
            
            $pic = [];
            if ($type == 'internal') {
                $pic = $faker->randomElements($internalPics, $faker->numberBetween(1, 2));
            } else {
                $pic = [$faker->name];
            }

            // Disposition logic
            $dispositionTo = [];
            if ($faker->boolean(70)) { // 70% chance of having disposition
                $dispositionTo = $faker->randomElements($councilMembers, $faker->numberBetween(1, 5));
            }

            Activity::create([
                'type' => $type,
                'name' => $type == 'internal' ? 'Rapat Internal: ' . $faker->sentence(3) : 'Undangan: ' . $faker->sentence(4),
                'date_time' => Carbon::now()->addDays($faker->numberBetween(-10, 30))->setTime($faker->numberBetween(8, 16), 0),
                'pic' => $pic,
                'status' => $faker->numberBetween(0, 3),
                'invitation_status' => $faker->numberBetween(0, 3),
                'invitation_type' => $type == 'internal' ? 'outbound' : 'inbound',
                'location_type' => $locationType,
                'location' => $locationType == 'offline' ? $faker->address : null,
                'meeting_link' => $locationType == 'online' ? 'https://zoom.us/j/' . $faker->randomNumber(9) : null,
                'dispo_note' => $faker->paragraph,
                'disposition_to' => $dispositionTo,
                'dresscode' => $faker->randomElement(['Batik Lengan Panjang', 'Bebas Rapi', 'Formal', null]),
            ]);
        }
    }
}
