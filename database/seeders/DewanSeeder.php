<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\Concerns\SeedsUsersSafely;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DewanSeeder extends Seeder
{
    use SeedsUsersSafely;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(OrganizationStructureSeeder::class);

        $divisions = Division::query()
            ->whereIn('name', [
                'Ketua DJSN',
                'Komisi Pengawasan Monitoring dan Evaluasi',
                'Komisi Kebijakan Umum',
            ])
            ->get()
            ->keyBy('name');

        $positions = Position::query()
            ->whereIn('code', ['ketua_djsn', 'ketua_komisi', 'wakil_komisi', 'anggota_komisi'])
            ->get()
            ->keyBy('code');

        $councilMembers = [
            [
                'name' => 'Nunung Nuryartono',
                'division' => 'Ketua DJSN',
                'position' => 'ketua_djsn',
                'order' => 10,
            ],
            [
                'name' => 'Muttaqien',
                'division' => 'Komisi Pengawasan Monitoring dan Evaluasi',
                'position' => 'ketua_komisi',
                'order' => 20,
            ],
            [
                'name' => 'Nikodemus Beriman Purba',
                'division' => 'Komisi Pengawasan Monitoring dan Evaluasi',
                'position' => 'wakil_komisi',
                'order' => 21,
            ],
            [
                'name' => 'Sudarto',
                'division' => 'Komisi Pengawasan Monitoring dan Evaluasi',
                'position' => 'anggota_komisi',
                'order' => 22,
            ],
            [
                'name' => 'Robben Rico',
                'division' => 'Komisi Pengawasan Monitoring dan Evaluasi',
                'position' => 'anggota_komisi',
                'order' => 23,
            ],
            [
                'name' => 'Mahesa Paranadipa Maykel',
                'division' => 'Komisi Pengawasan Monitoring dan Evaluasi',
                'position' => 'anggota_komisi',
                'order' => 24,
            ],
            [
                'name' => 'Syamsul Hidayat Pasaribu',
                'division' => 'Komisi Pengawasan Monitoring dan Evaluasi',
                'position' => 'anggota_komisi',
                'order' => 25,
            ],
            [
                'name' => 'Hermansyah',
                'division' => 'Komisi Pengawasan Monitoring dan Evaluasi',
                'position' => 'anggota_komisi',
                'order' => 26,
            ],
            [
                'name' => 'Paulus Agung Pambudhi',
                'division' => 'Komisi Kebijakan Umum',
                'position' => 'ketua_komisi',
                'order' => 30,
            ],
            [
                'name' => 'Agus Taufiqurrohman',
                'division' => 'Komisi Kebijakan Umum',
                'position' => 'wakil_komisi',
                'order' => 31,
            ],
            [
                'name' => 'Kunta Wibawa Dasa Nugraha',
                'division' => 'Komisi Kebijakan Umum',
                'position' => 'anggota_komisi',
                'order' => 32,
            ],
            [
                'name' => 'Indah Anggoro Putri',
                'division' => 'Komisi Kebijakan Umum',
                'position' => 'anggota_komisi',
                'order' => 33,
            ],
            [
                'name' => 'Rudi Purwono',
                'division' => 'Komisi Kebijakan Umum',
                'position' => 'anggota_komisi',
                'order' => 34,
            ],
            [
                'name' => 'Mickael Bobby Hoelman',
                'division' => 'Komisi Kebijakan Umum',
                'position' => 'anggota_komisi',
                'order' => 35,
            ],
            [
                'name' => 'Royanto Purba',
                'division' => 'Komisi Kebijakan Umum',
                'position' => 'anggota_komisi',
                'order' => 36,
            ],
        ];

        foreach ($councilMembers as $member) {
            $division = $divisions->get($member['division']);
            $position = $positions->get($member['position']);

            $this->seedUser(
                ['email' => $this->makeDjsnEmail($member['name'])],
                [
                    'name' => $member['name'],
                    'role' => User::ROLE_DEWAN,
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
        $cleanName = preg_replace('/^((Prof|Dr|Ir|Drs|Dra|H|R|rer\.pol)\.\s*)+/iu', '', $name);
        $cleanName = preg_replace('/,.+$/u', '', (string) $cleanName);

        $localPart = Str::of((string) $cleanName)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '.')
            ->trim('.')
            ->value();

        return ($localPart !== '' ? $localPart : 'user') . '@djsn.com';
    }
}
