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
