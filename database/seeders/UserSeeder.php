<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\Concerns\SeedsUsersSafely;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use SeedsUsersSafely;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(OrganizationStructureSeeder::class);

        $keuanganDivision = Division::where('name', 'Keuangan')->first();
        $keuanganPosition = Position::where('code', 'staf_keuangan')->first();

        // Super Admin bootstrap account
        $this->seedUser(
            ['email' => 'admin@djsn.com'],
            [
                'name' => 'Super Admin DJSN',
                'role' => User::ROLE_SUPER_ADMIN,
                'divisi' => 'Super Admin',
                'division_id' => null,
                'position_id' => null,
                'order' => 1,
            ]
        );

        // Sample view-only account
        $this->seedUser(
            ['email' => 'user@djsn.com'],
            [
                'name' => 'User Keuangan',
                'role' => User::ROLE_KEUANGAN,
                'divisi' => $keuanganDivision?->name,
                'division_id' => $keuanganDivision?->id,
                'position_id' => $keuanganPosition?->id,
                'order' => 999,
            ]
        );
    }
}
