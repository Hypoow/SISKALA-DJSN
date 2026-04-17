<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Database\Seeders\DewanSeeder;
use Database\Seeders\SekretariatSeeder;
use Database\Seeders\StaffSeeder;
use Database\Seeders\UserSeeder;
use Tests\Concerns\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeederSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_dewan_seeder_preserves_existing_custom_dewan_users(): void
    {
        $customUser = User::factory()->create([
            'email' => 'custom.dewan@example.com',
            'role' => User::ROLE_DEWAN,
            'divisi' => 'Komisi PME',
        ]);

        (new DewanSeeder())->run();

        $this->assertDatabaseHas('users', [
            'id' => $customUser->id,
            'email' => 'custom.dewan@example.com',
        ]);
    }

    public function test_user_seeder_does_not_reset_existing_passwords(): void
    {
        $originalHash = Hash::make('rahasia-lama');

        $user = User::factory()->create([
            'email' => 'admin@djsn.com',
            'password' => $originalHash,
            'role' => User::ROLE_LEGACY_ADMIN,
            'divisi' => 'TU',
        ]);

        (new UserSeeder())->run();

        $user->refresh();

        $this->assertSame($originalHash, $user->password);
        $this->assertTrue(Hash::check('rahasia-lama', $user->password));
    }

    public function test_sekretariat_seeder_preserves_existing_custom_secretariat_users(): void
    {
        $customUser = User::factory()->create([
            'email' => 'custom.secretariat@example.com',
            'role' => User::ROLE_SECRETARIAT,
            'divisi' => 'Sekretariat Khusus',
        ]);

        (new SekretariatSeeder())->run();

        $this->assertDatabaseHas('users', [
            'id' => $customUser->id,
            'email' => 'custom.secretariat@example.com',
        ]);
    }

    public function test_staff_seeder_does_not_duplicate_existing_staff_records(): void
    {
        Staff::create([
            'name' => 'Dwi Janatun Rahayu',
            'type' => 'sekretariat',
        ]);

        (new StaffSeeder())->run();

        $this->assertSame(1, Staff::where([
            'name' => 'Dwi Janatun Rahayu',
            'type' => 'sekretariat',
        ])->count());
    }
}
