<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DewanSeeder;
use Database\Seeders\OrganizationStructureSeeder;
use Database\Seeders\SekretariatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationStructureSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_structure_seeder_creates_divisions_and_positions_for_new_role_builder(): void
    {
        (new OrganizationStructureSeeder())->run();

        $this->assertDatabaseHas('divisions', [
            'name' => 'Komisi Kebijakan Umum',
            'short_label' => 'Komjakum',
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

    public function test_user_seeders_attach_members_to_divisions_positions_and_commissions(): void
    {
        (new OrganizationStructureSeeder())->run();
        (new DewanSeeder())->run();
        (new SekretariatSeeder())->run();

        /** @var User $ketuaKomisiPme */
        $ketuaKomisiPme = User::where('name', 'Muttaqien')->firstOrFail()->load(['division', 'position']);
        $sekretarisDjsn = User::where('name', 'Imron Rosadi')->firstOrFail()->load(['division', 'position']);
        $persidanganPme = User::where('name', 'Athi Rahmawati')->firstOrFail()->load(['division', 'position']);
        $taPme = User::where('name', 'Winda Sari')->firstOrFail()->load(['division', 'position']);

        $this->assertSame('Komisi Pengawasan Monitoring dan Evaluasi', $ketuaKomisiPme->division?->name);
        $this->assertSame('ketua_komisi', $ketuaKomisiPme->position?->code);
        $this->assertSame(User::ACCESS_PROFILE_DEWAN, $ketuaKomisiPme->resolved_access_profile);
        $this->assertSame(User::COMMISSION_PME, $ketuaKomisiPme->resolved_commission_code);

        $this->assertSame('Sekretaris DJSN', $sekretarisDjsn->division?->name);
        $this->assertSame('sekretaris_djsn', $sekretarisDjsn->position?->code);
        $this->assertSame(User::ACCESS_PROFILE_SET_DJSN, $sekretarisDjsn->resolved_access_profile);
        $this->assertTrue($sekretarisDjsn->canReceiveDisposition());

        $this->assertSame('Persidangan Komisi PME', $persidanganPme->division?->name);
        $this->assertSame('staf_persidangan', $persidanganPme->position?->code);
        $this->assertSame(User::ACCESS_PROFILE_PERSIDANGAN, $persidanganPme->resolved_access_profile);
        $this->assertSame(User::COMMISSION_PME, $persidanganPme->resolved_commission_code);

        $this->assertSame('Tenaga Ahli Komisi PME', $taPme->division?->name);
        $this->assertSame('tenaga_ahli', $taPme->position?->code);
        $this->assertSame(User::ACCESS_PROFILE_TENAGA_AHLI, $taPme->resolved_access_profile);
        $this->assertSame(User::COMMISSION_PME, $taPme->resolved_commission_code);
    }
}
