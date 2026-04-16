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
            Division::updateOrCreate(
                ['name' => $definition['name']],
                $definition
            );
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
                'order' => 10,
            ],
            [
                'name' => 'Komisi Pengawasan Monitoring dan Evaluasi',
                'short_label' => 'Komisi PME',
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_DEWAN, true, 'Komisi Pengawasan Monitoring dan Evaluasi'),
                'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
                'description' => 'Komisi Dewan untuk pengawasan, monitoring, dan evaluasi.',
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'commission_code' => User::COMMISSION_PME,
                'is_commission' => true,
                'order' => 20,
            ],
            [
                'name' => 'Komisi Kebijakan Umum',
                'short_label' => 'Komjakum',
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_DEWAN, true, 'Komisi Kebijakan Umum'),
                'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
                'description' => 'Komisi Dewan untuk kebijakan umum dan arah strategis.',
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'commission_code' => User::COMMISSION_KOMJAKUM,
                'is_commission' => true,
                'order' => 30,
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
                'order' => 40,
            ],
            [
                'name' => 'Bagian Umum',
                'short_label' => 'Bag. Umum',
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SECRETARIAT, false, 'Bagian Umum'),
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARIAT,
                'description' => 'Unit pengelola umum, tata usaha, dan dukungan administrasi.',
                'access_profile' => User::ACCESS_PROFILE_TATA_USAHA,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 50,
            ],
            [
                'name' => 'Bagian Persidangan',
                'short_label' => 'Bag. Persidangan',
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SECRETARIAT, false, 'Bagian Persidangan'),
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARIAT,
                'description' => 'Koordinasi persidangan, ringkasan rapat, MoM, bahan materi, dan tindak lanjut.',
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 60,
            ],
            [
                'name' => 'Sub Bagian Tata Usaha & Rumah Tangga',
                'short_label' => 'Subbag TU & RT',
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SECRETARIAT, false, 'Sub Bagian Tata Usaha & Rumah Tangga'),
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARIAT,
                'description' => 'Subbagian yang menangani tata usaha dan rumah tangga.',
                'access_profile' => User::ACCESS_PROFILE_TATA_USAHA,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 70,
            ],
            [
                'name' => 'Protokol dan Humas',
                'short_label' => 'ProtHum',
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SECRETARIAT, false, 'Protokol dan Humas'),
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARIAT,
                'description' => 'Unit dokumentasi, protokol, dan humas kegiatan.',
                'access_profile' => User::ACCESS_PROFILE_PROTHUM,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 80,
            ],
            [
                'name' => 'Tata Usaha',
                'short_label' => null,
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SECRETARIAT, false, 'Tata Usaha'),
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARIAT,
                'description' => 'Pelaksana administrasi kegiatan dan surat tugas.',
                'access_profile' => User::ACCESS_PROFILE_TATA_USAHA,
                'commission_code' => null,
                'is_commission' => false,
                'order' => 90,
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
                'order' => 100,
            ],
            [
                'name' => 'Persidangan Komisi PME',
                'short_label' => 'Persidangan PME',
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SUPPORT, false, 'Persidangan Komisi PME'),
                'structure_group' => Division::STRUCTURE_GROUP_SUPPORT,
                'description' => 'Pendamping Persidangan untuk Dewan Komisi PME.',
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
                'commission_code' => User::COMMISSION_PME,
                'is_commission' => false,
                'order' => 110,
            ],
            [
                'name' => 'Persidangan Komjakum',
                'short_label' => 'Persidangan Komjakum',
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SUPPORT, false, 'Persidangan Komjakum'),
                'structure_group' => Division::STRUCTURE_GROUP_SUPPORT,
                'description' => 'Pendamping Persidangan untuk Dewan Komjakum.',
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
                'commission_code' => User::COMMISSION_KOMJAKUM,
                'is_commission' => false,
                'order' => 120,
            ],
            [
                'name' => 'Tenaga Ahli Komisi PME',
                'short_label' => 'TA PME',
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SUPPORT, false, 'Tenaga Ahli Komisi PME'),
                'structure_group' => Division::STRUCTURE_GROUP_SUPPORT,
                'description' => 'Pendamping Tenaga Ahli untuk Dewan Komisi PME.',
                'access_profile' => User::ACCESS_PROFILE_TENAGA_AHLI,
                'commission_code' => User::COMMISSION_PME,
                'is_commission' => false,
                'order' => 130,
            ],
            [
                'name' => 'Tenaga Ahli Komjakum',
                'short_label' => 'TA Komjakum',
                'category' => Division::legacyCategoryFor(Division::STRUCTURE_GROUP_SUPPORT, false, 'Tenaga Ahli Komjakum'),
                'structure_group' => Division::STRUCTURE_GROUP_SUPPORT,
                'description' => 'Pendamping Tenaga Ahli untuk Dewan Komjakum.',
                'access_profile' => User::ACCESS_PROFILE_TENAGA_AHLI,
                'commission_code' => User::COMMISSION_KOMJAKUM,
                'is_commission' => false,
                'order' => 140,
            ],
        ];
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
                'disposition_group_label' => 'Set.DJSN',
                'report_target_label' => 'Sekretaris DJSN',
            ],
            [
                'name' => 'Kepala Bagian Umum',
                'code' => 'kabag_umum',
                'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_TATA_USAHA,
                'order' => 60,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => 'Kepala Bag. Umum',
            ],
            [
                'name' => 'Kepala Bagian Persidangan',
                'code' => 'kabag_persidangan',
                'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_PERSIDANGAN,
                'order' => 70,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => 'Plt/Kabag Persidangan',
            ],
            [
                'name' => 'Kepala Sub Bagian Tata Usaha & Rumah Tangga',
                'code' => 'kasubag_tu_rt',
                'structure_group' => User::STRUCTURE_GROUP_SEKRETARIAT,
                'access_profile' => User::ACCESS_PROFILE_TATA_USAHA,
                'order' => 80,
                'receives_disposition' => false,
                'disposition_group_label' => null,
                'report_target_label' => 'Kepala Sub. Bag. TU & Rumah Tangga',
            ],
            [
                'name' => 'Kepala Sub Bagian Protokol dan Humas',
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
