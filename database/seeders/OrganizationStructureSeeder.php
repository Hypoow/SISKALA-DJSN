<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizationStructureSeeder extends Seeder
{
    /**
     * Seed the application's core organization structure.
     */
    public function run(): void
    {
        foreach ($this->divisionDefinitions() as $definition) {
            $this->seedDivision($definition);
        }

        foreach ($this->positionDefinitions() as $definition) {
            $code = $definition['code'];
            unset($definition['code']);

            Position::updateOrCreate(
                ['code' => $code],
                $definition
            );
        }
    }

    private function divisionDefinitions(): array
    {
        return [
            [
                'name' => 'Ketua DJSN',
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_DEWAN, false, 'Ketua DJSN'),
                'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
                'description' => 'Pimpinan Dewan Jaminan Sosial Nasional.',
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 0,
            ],
            [
                'name' => 'Komisi PME',
                'aliases' => ['Komisi Pengawasan Monitoring dan Evaluasi'],
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_DEWAN, true, 'Komisi PME'),
                'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
                'description' => 'Komisi Dewan untuk pengawasan, monitoring, dan evaluasi.',
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'commission_code' => User::COMMISSION_PME,
                'is_commission' => true,
                'order' => 1,
            ],
            [
                'name' => 'Komjakum',
                'aliases' => ['Komisi Kebijakan Umum'],
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_DEWAN, true, 'Komjakum'),
                'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
                'description' => 'Komisi Dewan untuk kebijakan umum dan arah strategis.',
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'commission_code' => User::COMMISSION_KOMJAKUM,
                'is_commission' => true,
                'order' => 2,
            ],
            [
                'name' => 'Sekretaris DJSN',
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SECRETARY, false, 'Sekretaris DJSN'),
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARY,
                'description' => 'Pimpinan Sekretariat DJSN yang bersifat disposisi-based pada dashboard kalender.',
                'access_profile' => User::ACCESS_PROFILE_SET_DJSN,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 3,
            ],
            [
                'name' => 'Kabag. Persidangan',
                'aliases' => ['Bagian Persidangan', 'Kepala Bagian Persidangan'],
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SECRETARIAT, false, 'Kabag. Persidangan'),
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARIAT,
                'description' => 'Koordinasi persidangan, ringkasan rapat, MoM, bahan materi, dan tindak lanjut.',
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 4,
            ],
            [
                'name' => 'Tata Usaha',
                'aliases' => ['Bagian Umum', 'Sub Bagian Tata Usaha & Rumah Tangga'],
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SECRETARIAT, false, 'Tata Usaha'),
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARIAT,
                'description' => 'Pelaksana administrasi kegiatan dan surat tugas.',
                'access_profile' => User::ACCESS_PROFILE_TATA_USAHA,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 5,
            ],
            [
                'name' => 'ProtHum',
                'aliases' => ['Protokol dan Humas'],
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SECRETARIAT, false, 'ProtHum'),
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARIAT,
                'description' => 'Unit dokumentasi, protokol, dan humas kegiatan.',
                'access_profile' => User::ACCESS_PROFILE_PROTHUM,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 6,
            ],
            [
                'name' => 'Keuangan',
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SECRETARIAT, false, 'Keuangan'),
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARIAT,
                'description' => 'Akun view-only untuk pemantauan kebutuhan keuangan.',
                'access_profile' => User::ACCESS_PROFILE_KEUANGAN,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 7,
            ],
            [
                'name' => 'Persidangan Komisi PME',
                'aliases' => ['Persidangan PME'],
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SUPPORT, false, 'Persidangan Komisi PME'),
                'structure_group' => Division::STRUCTURE_GROUP_SUPPORT,
                'description' => 'Pendamping Persidangan untuk Dewan Komisi PME.',
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
                'commission_code' => User::COMMISSION_PME,
                'is_commission' => false,
                'order' => 8,
            ],
            [
                'name' => 'Persidangan Komjakum',
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SUPPORT, false, 'Persidangan Komjakum'),
                'structure_group' => Division::STRUCTURE_GROUP_SUPPORT,
                'description' => 'Pendamping Persidangan untuk Dewan Komjakum.',
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
                'commission_code' => User::COMMISSION_KOMJAKUM,
                'is_commission' => false,
                'order' => 9,
            ],
            [
                'name' => 'TA Komisi PME',
                'aliases' => ['Tenaga Ahli Komisi PME', 'TA PME'],
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SUPPORT, false, 'TA Komisi PME'),
                'structure_group' => Division::STRUCTURE_GROUP_SUPPORT,
                'description' => 'Pendamping Tenaga Ahli untuk Dewan Komisi PME.',
                'access_profile' => User::ACCESS_PROFILE_TENAGA_AHLI,
                'commission_code' => User::COMMISSION_PME,
                'is_commission' => false,
                'order' => 10,
            ],
            [
                'name' => 'TA Komjakum',
                'aliases' => ['Tenaga Ahli Komjakum'],
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SUPPORT, false, 'TA Komjakum'),
                'structure_group' => Division::STRUCTURE_GROUP_SUPPORT,
                'description' => 'Pendamping Tenaga Ahli untuk Dewan Komjakum.',
                'access_profile' => User::ACCESS_PROFILE_TENAGA_AHLI,
                'commission_code' => User::COMMISSION_KOMJAKUM,
                'is_commission' => false,
                'order' => 11,
            ],
        ];
    }

    private function seedDivision(array $definition): Division
    {
        $lookupNames = collect([$definition['name'], ...($definition['aliases'] ?? [])])
            ->filter(fn ($name) => is_string($name) && trim($name) !== '')
            ->map(fn (string $name) => trim($name))
            ->unique()
            ->values();

        unset($definition['aliases']);

        $matches = Division::query()
            ->whereIn('name', $lookupNames->all())
            ->orderBy('id')
            ->get();

        $division = $matches->firstWhere('name', $definition['name']) ?? $matches->first();
        $previousName = $division?->name;

        if ($division) {
            $division->fill($definition);
            $division->save();
        } else {
            $division = Division::create($definition);
        }

        $this->syncUsersToDivisionName($division, $previousName);

        $matches
            ->reject(fn (Division $match) => $match->id === $division->id)
            ->each(function (Division $legacyDivision) use ($division) {
                $this->moveUsersToDivision($legacyDivision, $division);
                $legacyDivision->delete();
            });

        return $division;
    }

    private function syncUsersToDivisionName(Division $division, ?string $previousName = null): void
    {
        $query = User::query()->where('division_id', $division->id);

        if ($previousName !== null && $previousName !== $division->name) {
            $query->orWhere('divisi', $previousName);
        }

        $query->update([
            'division_id' => $division->id,
            'divisi' => $division->name,
        ]);
    }

    private function moveUsersToDivision(Division $source, Division $target): void
    {
        User::query()
            ->where('division_id', $source->id)
            ->orWhere('divisi', $source->name)
            ->update([
                'division_id' => $target->id,
                'divisi' => $target->name,
            ]);
    }

    private function positionDefinitions(): array
    {
        return [
            [
                'name' => 'Ketua DJSN',
                'code' => 'ketua_djsn',
                'structure_group' => User::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'order' => 10,
                'receives_disposition' => true,
                'disposition_group_label' => null,
                'report_target_label' => 'Ketua DJSN',
            ],
            [
                'name' => 'Ketua Komisi',
                'code' => 'ketua_komisi',
                'structure_group' => User::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'order' => 20,
                'receives_disposition' => true,
                'disposition_group_label' => null,
                'report_target_label' => null,
            ],
            [
                'name' => 'Wakil Komisi',
                'code' => 'wakil_komisi',
                'structure_group' => User::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'order' => 30,
                'receives_disposition' => true,
                'disposition_group_label' => null,
                'report_target_label' => null,
            ],
            [
                'name' => 'Anggota Komisi',
                'code' => 'anggota_komisi',
                'structure_group' => User::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'order' => 40,
                'receives_disposition' => true,
                'disposition_group_label' => null,
                'report_target_label' => null,
            ],
            [
                'name' => 'Sekretaris DJSN',
                'code' => 'sekretaris_djsn',
                'structure_group' => User::STRUCTURE_GROUP_SET_DJSN,
                'access_profile' => User::ACCESS_PROFILE_SET_DJSN,
                'order' => 50,
                'receives_disposition' => true,
                'disposition_group_label' => 'Sekretaris DJSN',
                'report_target_label' => 'Sekretaris DJSN',
            ],
            [
                'name' => 'Kabag Umum',
                'code' => 'kabag_umum',
                'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_TATA_USAHA,
                'order' => 60,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => 'Kepala Bag. Umum',
            ],
            [
                'name' => 'Kabag Persidangan',
                'code' => 'kabag_persidangan',
                'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
                'order' => 70,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => 'Plt/Kabag Persidangan',
            ],
            [
                'name' => 'Kasubag TU & Rumah Tangga',
                'code' => 'kasubag_tu_rt',
                'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_TATA_USAHA,
                'order' => 80,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => 'Kepala Sub. Bag. TU & Rumah Tangga',
            ],
            [
                'name' => 'Kasubag Prothum',
                'code' => 'kasubag_protokol_humas',
                'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_PROTHUM,
                'order' => 90,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => 'Kepala Sub. Bag. Protokol & Humas',
            ],
            [
                'name' => 'Tata Usaha',
                'code' => 'staf_tata_usaha',
                'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_TATA_USAHA,
                'order' => 100,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => null,
            ],
            [
                'name' => 'Persidangan',
                'code' => 'staf_persidangan',
                'structure_group' => User::STRUCTURE_GROUP_PENDAMPING,
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
                'order' => 110,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => null,
            ],
            [
                'name' => 'Protokol dan Humas',
                'code' => 'staf_prothum',
                'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_PROTHUM,
                'order' => 120,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => null,
            ],
            [
                'name' => 'Keuangan',
                'code' => 'staf_keuangan',
                'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_KEUANGAN,
                'order' => 130,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => null,
            ],
            [
                'name' => 'Tenaga Ahli',
                'code' => 'tenaga_ahli',
                'structure_group' => User::STRUCTURE_GROUP_PENDAMPING,
                'access_profile' => User::ACCESS_PROFILE_TENAGA_AHLI,
                'order' => 140,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => null,
            ],
        ];
    }
}
