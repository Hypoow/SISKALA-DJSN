<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\Position;
use App\Models\User;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class MasterDataStructurePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_structure_builder_and_account_forms(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $division = Division::create([
            'name' => 'Komjakum',
            'short_label' => null,
            'category' => 'Komisi',
            'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
            'access_profile' => User::ACCESS_PROFILE_DEWAN,
            'commission_code' => 'komjakum',
            'is_commission' => true,
            'order' => 1,
        ]);

        $position = Position::create([
            'name' => 'Ketua Komisi',
            'code' => 'ketua_komisi',
            'structure_group' => User::STRUCTURE_GROUP_DEWAN,
            'access_profile' => User::ACCESS_PROFILE_DEWAN,
            'receives_disposition' => true,
            'order' => 1,
        ]);

        $managedUser = User::factory()->create([
            'role' => User::ROLE_DEWAN,
            'division_id' => $division->id,
            'position_id' => $position->id,
            'divisi' => $division->name,
        ]);

        $this->actingAs($admin)
            ->get(route('master-data.divisions'))
            ->assertOk()
            ->assertSeeText('Builder Struktur Role')
            ->assertSeeText('Kelompok Akun')
            ->assertSeeText('Unit Kerja')
            ->assertSeeText('Master Jabatan')
            ->assertDontSee('Label Ringkas');

        $this->actingAs($admin)
            ->get(route('master-data.create'))
            ->assertOk()
            ->assertSee('Tambah Akun Baru')
            ->assertSee('Penempatan Struktur')
            ->assertSee('Ringkasan Hak Akses')
            ->assertDontSee('Sapaan');

        $this->actingAs($admin)
            ->get(route('master-data.edit', $managedUser))
            ->assertOk()
            ->assertSee('Edit Akun Pengguna')
            ->assertSee('Penempatan Struktur')
            ->assertSee('Ringkasan Hak Akses')
            ->assertDontSee('Sapaan');
    }
}
