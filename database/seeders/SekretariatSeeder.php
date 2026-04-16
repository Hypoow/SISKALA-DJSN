<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\Concerns\SeedsUsersSafely;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SekretariatSeeder extends Seeder
{
    use SeedsUsersSafely;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(OrganizationStructureSeeder::class);

        $divisions = Division::query()->get()->keyBy('name');
        $positions = Position::query()->get()->keyBy('code');

        $members = [
            [
                'name' => 'Imron Rosadi',
                'role' => User::ROLE_SECRETARIAT,
                'division' => 'Sekretaris DJSN',
                'position' => 'sekretaris_djsn',
                'order' => 110,
            ],
            [
                'name' => 'Dwi Janatun Rahayu',
                'role' => User::ROLE_TATA_USAHA,
                'division' => 'Bagian Umum',
                'position' => 'kabag_umum',
                'order' => 120,
            ],
            [
                'name' => 'Wenny Kartika Ayunungtyas',
                'role' => User::ROLE_PERSIDANGAN,
                'division' => 'Bagian Persidangan',
                'position' => 'kabag_persidangan',
                'order' => 130,
            ],
            [
                'name' => 'Annisa',
                'role' => User::ROLE_TATA_USAHA,
                'division' => 'Sub Bagian Tata Usaha & Rumah Tangga',
                'position' => 'kasubag_tu_rt',
                'order' => 140,
            ],
            [
                'name' => 'Eko Sudarmawan',
                'role' => User::ROLE_BAGIAN_UMUM,
                'division' => 'Protokol dan Humas',
                'position' => 'kasubag_protokol_humas',
                'order' => 150,
            ],
            [
                'name' => 'Budi Mulyono',
                'role' => User::ROLE_TATA_USAHA,
                'division' => 'Tata Usaha',
                'position' => 'staf_tata_usaha',
                'order' => 160,
            ],
            [
                'name' => 'Fatoni Mahendra Kurniawan',
                'role' => User::ROLE_TATA_USAHA,
                'division' => 'Tata Usaha',
                'position' => 'staf_tata_usaha',
                'order' => 161,
            ],
            [
                'name' => 'Athi Rahmawati',
                'role' => User::ROLE_PERSIDANGAN,
                'division' => 'Persidangan Komisi PME',
                'position' => 'staf_persidangan',
                'order' => 170,
            ],
            [
                'name' => 'Eka Yeyen Pertiwi',
                'role' => User::ROLE_PERSIDANGAN,
                'division' => 'Persidangan Komisi PME',
                'position' => 'staf_persidangan',
                'order' => 171,
            ],
            [
                'name' => 'Azzahra',
                'role' => User::ROLE_PERSIDANGAN,
                'division' => 'Persidangan Komjakum',
                'position' => 'staf_persidangan',
                'order' => 172,
            ],
            [
                'name' => 'Iis Okta Nurria Putri',
                'role' => User::ROLE_PERSIDANGAN,
                'division' => 'Persidangan Komjakum',
                'position' => 'staf_persidangan',
                'order' => 173,
            ],
            [
                'name' => 'Rahmi Syafina',
                'role' => User::ROLE_BAGIAN_UMUM,
                'division' => 'Protokol dan Humas',
                'position' => 'staf_prothum',
                'order' => 180,
            ],
            [
                'name' => 'Sarah Puspita Rahmasari',
                'role' => User::ROLE_BAGIAN_UMUM,
                'division' => 'Protokol dan Humas',
                'position' => 'staf_prothum',
                'order' => 181,
            ],
            [
                'name' => 'Nabila Ika Putri',
                'role' => User::ROLE_KEUANGAN,
                'division' => 'Keuangan',
                'position' => 'staf_keuangan',
                'order' => 190,
            ],
            [
                'name' => 'Qafyl Ramadhano Mufti',
                'role' => User::ROLE_KEUANGAN,
                'division' => 'Keuangan',
                'position' => 'staf_keuangan',
                'order' => 191,
            ],
            [
                'name' => 'Winda Sari',
                'role' => User::ROLE_TA,
                'division' => 'Tenaga Ahli Komisi PME',
                'position' => 'tenaga_ahli',
                'order' => 210,
            ],
            [
                'name' => 'Muhammad Iqbal Khatami',
                'role' => User::ROLE_TA,
                'division' => 'Tenaga Ahli Komisi PME',
                'position' => 'tenaga_ahli',
                'order' => 211,
            ],
            [
                'name' => 'Sandro Andriawan',
                'role' => User::ROLE_TA,
                'division' => 'Tenaga Ahli Komisi PME',
                'position' => 'tenaga_ahli',
                'order' => 212,
            ],
            [
                'name' => 'Fahmi Hakam',
                'role' => User::ROLE_TA,
                'division' => 'Tenaga Ahli Komisi PME',
                'position' => 'tenaga_ahli',
                'order' => 213,
            ],
            [
                'name' => 'Mukhlisah',
                'role' => User::ROLE_TA,
                'division' => 'Tenaga Ahli Komisi PME',
                'position' => 'tenaga_ahli',
                'order' => 214,
            ],
            [
                'name' => 'Khairunnas',
                'role' => User::ROLE_TA,
                'division' => 'Tenaga Ahli Komjakum',
                'position' => 'tenaga_ahli',
                'order' => 220,
            ],
            [
                'name' => 'Lutfi Aulia Ulfah',
                'role' => User::ROLE_TA,
                'division' => 'Tenaga Ahli Komjakum',
                'position' => 'tenaga_ahli',
                'order' => 221,
            ],
            [
                'name' => 'Anindhia Salsabila',
                'role' => User::ROLE_TA,
                'division' => 'Tenaga Ahli Komjakum',
                'position' => 'tenaga_ahli',
                'order' => 222,
            ],
            [
                'name' => 'Averin Dian Boruna Sidauruk',
                'role' => User::ROLE_TA,
                'division' => 'Tenaga Ahli Komjakum',
                'position' => 'tenaga_ahli',
                'order' => 223,
            ],
            [
                'name' => "Sa'dan Mubarok",
                'role' => User::ROLE_TA,
                'division' => 'Tenaga Ahli Komjakum',
                'position' => 'tenaga_ahli',
                'order' => 224,
            ],
        ];

        foreach ($members as $member) {
            $division = $divisions->get($member['division']);
            $position = $positions->get($member['position']);

            $this->seedUser(
                ['email' => $this->makeDjsnEmail($member['name'])],
                [
                    'name' => $member['name'],
                    'role' => $member['role'],
                    'divisi' => $division?->name,
                    'division_id' => $division?->id,
                    'position_id' => $position?->id,
                    'order' => $member['order'],
                ]
            );
        }
    }

    private function makeDjsnEmail(string $name): string
    {
        $localPart = Str::of($name)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '.')
            ->trim('.')
            ->value();

        return ($localPart !== '' ? $localPart : 'user') . '@djsn.com';
    }
}
