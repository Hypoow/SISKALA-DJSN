<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Database\Seeders\DewanSeeder;
use Database\Seeders\OrganizationStructureSeeder;
use Database\Seeders\SekretariatSeeder;
use Database\Seeders\StaffSeeder;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class OrganizationStructureSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_structure_seeder_creates_divisions_and_positions_for_new_role_builder(): void
    {
        (new OrganizationStructureSeeder())->run();

        $this->assertDatabaseHas('divisions', [
            'name' => 'Komjakum',
            'short_label' => null,
            'structure_group' => 'dewan',
            'access_profile' => 'dewan',
            'commission_code' => 'komjakum',
            'is_commission' => true,
        ]);

        $this->assertDatabaseHas('divisions', [
            'name' => 'Persidangan Komisi PME',
            'structure_group' => 'pendamping_dewan',
            'access_profile' => 'persidangan',
            'commission_code' => 'pme',
        ]);

        $this->assertDatabaseHas('divisions', [
            'name' => 'Kabag. Persidangan',
            'short_label' => null,
            'structure_group' => 'sekretariat_djsn',
            'access_profile' => 'persidangan',
            'order' => 4,
        ]);

        $this->assertDatabaseHas('divisions', [
            'name' => 'Tata Usaha',
            'structure_group' => 'sekretariat_djsn',
            'access_profile' => 'tata_usaha',
            'order' => 5,
        ]);

        $this->assertDatabaseMissing('divisions', [
            'name' => 'Bagian Umum',
        ]);

        $this->assertDatabaseMissing('divisions', [
            'name' => 'Sub Bagian Tata Usaha & Rumah Tangga',
        ]);

        $this->assertDatabaseHas('positions', [
            'code' => 'sekretaris_djsn',
            'name' => 'Sekretaris DJSN',
            'structure_group' => 'set_djsn',
            'access_profile' => 'set_djsn',
            'receives_disposition' => true,
        ]);

        $this->assertDatabaseHas('positions', [
            'code' => 'tenaga_ahli',
            'name' => 'Tenaga Ahli',
            'structure_group' => 'pendamping',
            'access_profile' => 'tenaga_ahli',
        ]);
    }

    public function test_secretariat_accounts_and_support_staff_are_seeded_in_the_correct_tables(): void
    {
        (new OrganizationStructureSeeder())->run();
        (new DewanSeeder())->run();
        (new SekretariatSeeder())->run();
        (new StaffSeeder())->run();

        /** @var User $ketuaKomisiPme */
        $ketuaKomisiPme = User::where('name', 'Muttaqien')->firstOrFail()->load(['division', 'position']);
        $sekretarisDjsn = User::where('name', 'Imron Rosadi')->firstOrFail()->load(['division', 'position']);

        $this->assertSame('Komisi PME', $ketuaKomisiPme->division?->name);
        $this->assertSame('ketua_komisi', $ketuaKomisiPme->position?->code);
        $this->assertSame(User::ACCESS_PROFILE_DEWAN, $ketuaKomisiPme->resolved_access_profile);
        $this->assertSame(User::COMMISSION_PME, $ketuaKomisiPme->resolved_commission_code);

        $this->assertSame('Sekretaris DJSN', $sekretarisDjsn->division?->name);
        $this->assertSame('sekretaris_djsn', $sekretarisDjsn->position?->code);
        $this->assertSame(User::ACCESS_PROFILE_SET_DJSN, $sekretarisDjsn->resolved_access_profile);
        $this->assertTrue($sekretarisDjsn->canReceiveDisposition());

        $this->assertDatabaseMissing('users', ['name' => 'Dwi Janatun Rahayu']);
        $this->assertDatabaseMissing('users', ['name' => 'Wenny Kartika Ayunungtyas']);
        $this->assertDatabaseMissing('users', ['name' => 'Winda Sari']);

        $this->assertDatabaseHas('staff', [
            'name' => 'Dwi Janatun Rahayu',
            'type' => 'sekretariat',
        ]);

        $this->assertDatabaseHas('staff', [
            'name' => 'Wenny Kartika Ayunungtyas',
            'type' => 'sekretariat',
        ]);

        $this->assertDatabaseHas('staff', [
            'name' => 'Winda Sari',
            'type' => 'ta',
        ]);

        $this->assertSame(25, Staff::where('type', 'sekretariat')->count());
        $this->assertSame(10, Staff::where('type', 'ta')->count());
    }

    public function test_structure_seeder_merges_legacy_secretariat_units_into_the_new_builder_layout(): void
    {
        $bagianUmum = \App\Models\Division::create([
            'name' => 'Bagian Umum',
            'category' => 'Sekretariat DJSN',
            'structure_group' => 'sekretariat_djsn',
            'access_profile' => 'tata_usaha',
            'order' => 99,
        ]);

        $subBagianTu = \App\Models\Division::create([
            'name' => 'Sub Bagian Tata Usaha & Rumah Tangga',
            'category' => 'Sekretariat DJSN',
            'structure_group' => 'sekretariat_djsn',
            'access_profile' => 'tata_usaha',
            'order' => 100,
        ]);

        $bagianPersidangan = \App\Models\Division::create([
            'name' => 'Bagian Persidangan',
            'category' => 'Sekretariat DJSN',
            'structure_group' => 'sekretariat_djsn',
            'access_profile' => 'persidangan',
            'order' => 101,
        ]);

        $userBagianUmum = User::factory()->create([
            'division_id' => $bagianUmum->id,
            'divisi' => $bagianUmum->name,
        ]);

        $userSubBagianTu = User::factory()->create([
            'division_id' => $subBagianTu->id,
            'divisi' => $subBagianTu->name,
        ]);

        $userBagianPersidangan = User::factory()->create([
            'division_id' => $bagianPersidangan->id,
            'divisi' => $bagianPersidangan->name,
        ]);

        (new OrganizationStructureSeeder())->run();

        $this->assertDatabaseMissing('divisions', ['name' => 'Bagian Umum']);
        $this->assertDatabaseMissing('divisions', ['name' => 'Sub Bagian Tata Usaha & Rumah Tangga']);
        $this->assertDatabaseMissing('divisions', ['name' => 'Bagian Persidangan']);

        $tataUsaha = \App\Models\Division::where('name', 'Tata Usaha')->firstOrFail();
        $kabagPersidangan = \App\Models\Division::where('name', 'Kabag. Persidangan')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'id' => $userBagianUmum->id,
            'division_id' => $tataUsaha->id,
            'divisi' => 'Tata Usaha',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $userSubBagianTu->id,
            'division_id' => $tataUsaha->id,
            'divisi' => 'Tata Usaha',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $userBagianPersidangan->id,
            'division_id' => $kabagPersidangan->id,
            'divisi' => 'Kabag. Persidangan',
        ]);
    }
}
