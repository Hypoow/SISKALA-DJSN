<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Division;
use App\Models\Position;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserAccessRulesTest extends TestCase
{
    public function test_super_admin_has_full_access(): void
    {
        $user = $this->makeUser('Super Admin', 'super_admin', 'Sekretariat DJSN');

        $this->assertTrue($user->canAccessAdminArea());
        $this->assertTrue($user->canManageActivities());
        $this->assertTrue($user->canManagePostActivity());
        $this->assertTrue($user->canManageDocumentation());
        $this->assertTrue($user->canAccessH1Report());
        $this->assertTrue($user->canViewAllActivities());
    }

    public function test_tata_usaha_access_can_follow_division_configuration(): void
    {
        $tu = $this->makeUser('Staf TU', User::ROLE_TATA_USAHA, 'Tata Usaha');
        $kabagUmum = $this->makeUser(
            'Kabag Umum',
            User::ROLE_SECRETARIAT,
            'Tata Usaha',
            'Sekretariat DJSN',
            new Position(['name' => 'Kepala Bagian Umum']),
            [],
            ['access_profile' => User::ACCESS_PROFILE_TATA_USAHA]
        );
        $kasubagTu = $this->makeUser(
            'Kasubag TU',
            User::ROLE_SECRETARIAT,
            'Tata Usaha',
            'Sekretariat DJSN',
            new Position(['name' => 'Kepala Sub Tata Usaha dan Rumah Tangga']),
            [],
            ['access_profile' => User::ACCESS_PROFILE_TATA_USAHA]
        );

        $this->assertTrue($tu->canManageActivities());
        $this->assertTrue($tu->canAccessH1Report());

        $this->assertTrue($kabagUmum->canManageActivities());
        $this->assertTrue($kabagUmum->canReceiveDisposition());

        $this->assertTrue($kasubagTu->canManageActivities());
        $this->assertTrue($kasubagTu->canReceiveDisposition());
    }

    public function test_persidangan_can_manage_post_activity_without_general_activity_crud(): void
    {
        $user = $this->makeUser('Persidangan PME', User::ROLE_PERSIDANGAN, 'Komisi PME');

        $this->assertTrue($user->canManagePostActivity());
        $this->assertTrue($user->canManageFollowUp());
        $this->assertTrue($user->canManageTopics());
        $this->assertFalse($user->canManageActivities());
        $this->assertSame(['pme'], $user->getCommissionKeys());
    }

    public function test_protokol_humas_and_keuangan_follow_their_jobdesk(): void
    {
        $protokol = $this->makeUser('Kasubag Protokol', User::ROLE_BAGIAN_UMUM, 'Kepala Sub Protokol dan Kehumasan');
        $keuangan = $this->makeUser('Staf Keuangan', User::ROLE_KEUANGAN, 'Keuangan');

        $this->assertTrue($protokol->canManageDocumentation());
        $this->assertTrue($protokol->canReceiveDisposition());
        $this->assertFalse($protokol->canManageActivities());

        $this->assertTrue($keuangan->canViewAllActivities());
        $this->assertFalse($keuangan->canManageActivities());
        $this->assertFalse($keuangan->canManageDocumentation());
    }

    public function test_dewan_visibility_is_direct_only(): void
    {
        $user = $this->makeUser('Anggota Dewan PME', User::ROLE_DEWAN, 'Komisi PME');

        $visibleActivity = new Activity([
            'disposition_to' => ['Anggota Dewan PME'],
            'pic' => [],
        ]);

        $hiddenActivity = new Activity([
            'disposition_to' => ['Anggota Dewan Lain'],
            'pic' => [],
        ]);

        $this->assertTrue($user->canViewActivity($visibleActivity));
        $this->assertFalse($user->canViewActivity($hiddenActivity));
    }

    public function test_ta_and_persidangan_can_see_activity_when_same_commission_dewan_is_disposed(): void
    {
        $activity = new Activity([
            'disposition_to' => ['Dewan Komisi PME'],
            'pic' => [],
        ]);

        $ta = new class([
            'name' => 'TA PME',
            'role' => User::ROLE_TA,
            'divisi' => 'Komisi PME',
        ]) extends User {
            public function getCommissionDewanNames(): array
            {
                return ['Dewan Komisi PME'];
            }
        };
        $ta->setRelation('division', new Division([
            'name' => 'Komisi PME',
            'category' => 'Komisi',
        ]));

        $persidangan = new class([
            'name' => 'Persidangan PME',
            'role' => User::ROLE_PERSIDANGAN,
            'divisi' => 'Komisi PME',
        ]) extends User {
            public function getCommissionDewanNames(): array
            {
                return ['Dewan Komisi PME'];
            }
        };
        $persidangan->setRelation('division', new Division([
            'name' => 'Komisi PME',
            'category' => 'Komisi',
        ]));

        $this->assertTrue($ta->canViewActivity($activity));
        $this->assertTrue($persidangan->canViewActivity($activity));
    }

    public function test_existing_secretariat_title_variants_are_recognized_as_disposition_targets(): void
    {
        $sekretaris = $this->makeUser('Sekretaris', User::ROLE_SECRETARIAT, 'Sekretaris DJSN');
        $kabagPersidangan = $this->makeUser('Plt Persidangan', User::ROLE_SECRETARIAT, 'Plt.Kepala Bagian Persidangan');
        $kasubagTu = $this->makeUser('Kasubag TU', User::ROLE_SECRETARIAT, 'Kepala Sub Tata Usaha dan Rumah Tangga');
        $kasubagHumas = $this->makeUser('Kasubag Humas', User::ROLE_SECRETARIAT, 'Kepala Sub Protokol dan Kehumasan');

        $this->assertTrue($sekretaris->canReceiveDisposition());
        $this->assertTrue($kabagPersidangan->canReceiveDisposition());
        $this->assertTrue($kasubagTu->canReceiveDisposition());
        $this->assertTrue($kasubagHumas->canReceiveDisposition());
    }

    public function test_set_djsn_is_disposition_based_and_not_global_viewer(): void
    {
        $position = new Position([
            'name' => 'Sekretaris DJSN',
            'access_profile' => User::ACCESS_PROFILE_SET_DJSN,
            'receives_disposition' => true,
            'disposition_group_label' => 'Sekretaris DJSN',
        ]);

        $user = $this->makeUser('Sekretaris DJSN', User::ROLE_SECRETARIAT, 'Sekretariat DJSN', 'Sekretariat DJSN', $position);

        $visibleActivity = new Activity([
            'disposition_to' => ['Sekretaris DJSN'],
            'pic' => [],
        ]);

        $hiddenActivity = new Activity([
            'disposition_to' => ['Dewan Komisi PME'],
            'pic' => [],
        ]);

        $this->assertTrue($user->canReceiveDisposition());
        $this->assertFalse($user->canViewAllActivities());
        $this->assertTrue($user->canViewActivity($visibleActivity));
        $this->assertFalse($user->canViewActivity($hiddenActivity));
    }

    public function test_position_defaults_can_enable_disposition_and_report_labels_without_special_role(): void
    {
        $position = new Position([
            'name' => 'Sekretaris DJSN',
            'receives_disposition' => true,
            'disposition_group_label' => 'Sekretariat DJSN',
            'report_target_label' => 'Sekretaris DJSN',
        ]);

        $user = $this->makeUser('Sekretaris Baru', User::ROLE_USER, 'Sekretariat DJSN', 'Sekretariat DJSN', $position);

        $this->assertTrue($user->canReceiveDisposition());
        $this->assertSame('Sekretaris DJSN', $user->disposition_group_label);
        $this->assertSame('Sekretaris DJSN', $user->resolved_report_target_label);
    }

    public function test_user_override_can_disable_disposition_even_if_position_default_is_enabled(): void
    {
        $position = new Position([
            'name' => 'Kepala Bagian Umum',
            'receives_disposition' => true,
            'disposition_group_label' => 'Sekretariat DJSN',
            'report_target_label' => 'Kepala Bagian Umum',
        ]);

        $user = $this->makeUser(
            'Kabag Override',
            User::ROLE_USER,
            'Bagian Umum',
            'Sekretariat DJSN',
            $position,
            [
                'receives_disposition' => false,
                'disposition_group_label' => 'Tidak Dipakai',
            ]
        );

        $this->assertFalse($user->canReceiveDisposition());
        $this->assertSame('Tidak Dipakai', $user->disposition_group_label);
    }

    private function makeUser(
        string $name,
        string $role,
        string $divisionName,
        string $category = 'Sekretariat DJSN',
        ?Position $position = null,
        array $overrides = [],
        array $divisionOverrides = []
    ): User {
        $user = new User(array_merge([
            'name' => $name,
            'role' => $role,
            'divisi' => $divisionName,
        ], $overrides));

        $user->setRelation('division', new Division(array_merge([
            'name' => $divisionName,
            'category' => $category,
        ], $divisionOverrides)));

        if ($position) {
            $user->setRelation('position', $position);
        }

        return $user;
    }
}
