<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class SuperAdminProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_primary_super_admin_cannot_be_deleted(): void
    {
        $primarySuperAdmin = User::factory()->create([
            'name' => 'Super Admin DJSN',
            'email' => 'admin@djsn.com',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Super Admin',
        ]);

        $actingAdmin = User::factory()->create([
            'name' => 'Super Admin Operasional',
            'email' => 'ops@djsn.com',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);

        $response = $this->actingAs($actingAdmin)->delete(route('master-data.destroy', $primarySuperAdmin));

        $response->assertRedirect(route('master-data.index'));
        $response->assertSessionHas('error', 'Akun Super Admin Utama tidak dapat dihapus.');
        $this->assertDatabaseHas('users', [
            'id' => $primarySuperAdmin->id,
            'email' => 'admin@djsn.com',
        ]);
    }

    public function test_secondary_super_admin_can_be_deleted(): void
    {
        User::factory()->create([
            'name' => 'Super Admin DJSN',
            'email' => 'admin@djsn.com',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Super Admin',
        ]);

        $actingAdmin = User::factory()->create([
            'name' => 'Super Admin Operasional',
            'email' => 'ops@djsn.com',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);

        $secondarySuperAdmin = User::factory()->create([
            'name' => 'Super Admin Cadangan',
            'email' => 'backup@djsn.com',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);

        $response = $this->actingAs($actingAdmin)->delete(route('master-data.destroy', $secondarySuperAdmin));

        $response->assertRedirect(route('master-data.index'));
        $response->assertSessionHas('success', 'Akun berhasil dihapus.');
        $this->assertDatabaseMissing('users', [
            'id' => $secondarySuperAdmin->id,
        ]);
    }
}
