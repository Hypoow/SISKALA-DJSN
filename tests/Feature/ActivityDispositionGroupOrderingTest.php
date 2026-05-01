<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\Position;
use App\Models\User;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ActivityDispositionGroupOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_uses_master_disposition_group_order(): void
    {
        $user = $this->createAdminUser();

        $ketuaDivision = Division::query()->updateOrCreate(
            ['name' => 'Ketua DJSN'],
            [
                'category' => 'Ketua DJSN',
                'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'order' => 0,
            ]
        );
        $pmeDivision = Division::query()->updateOrCreate(
            ['name' => 'Komisi PME'],
            [
                'category' => 'Komisi',
                'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'commission_code' => 'pme',
                'is_commission' => true,
                'order' => 1,
            ]
        );
        $komjakumDivision = Division::query()->updateOrCreate(
            ['name' => 'Komjakum'],
            [
                'category' => 'Komisi',
                'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'commission_code' => 'komjakum',
                'is_commission' => true,
                'order' => 2,
            ]
        );
        $sekretarisDivision = Division::query()->updateOrCreate(
            ['name' => 'Sekretaris DJSN'],
            [
                'category' => 'Sekretariat DJSN',
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARY,
                'access_profile' => User::ACCESS_PROFILE_SET_DJSN,
                'order' => 3,
            ]
        );

        $dewanPosition = Position::query()->updateOrCreate(
            ['code' => 'anggota_dewan_urut_disposisi'],
            [
                'name' => 'Anggota Dewan Urut Disposisi',
                'structure_group' => User::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'order' => 1,
                'receives_disposition' => true,
            ]
        );
        $sekretarisPosition = Position::query()->updateOrCreate(
            ['code' => 'sekretaris_djsn_urut_disposisi'],
            [
                'name' => 'Sekretaris DJSN Urut Disposisi',
                'structure_group' => User::STRUCTURE_GROUP_SET_DJSN,
                'access_profile' => User::ACCESS_PROFILE_SET_DJSN,
                'order' => 1,
                'receives_disposition' => true,
                'disposition_group_label' => 'Sekretaris DJSN',
            ]
        );

        User::factory()->create([
            'name' => 'Ketua Uji Urutan',
            'email' => 'ketua-urutan@example.test',
            'role' => User::ROLE_DEWAN,
            'division_id' => $ketuaDivision->id,
            'position_id' => $dewanPosition->id,
            'divisi' => $ketuaDivision->name,
            'order' => 1,
        ]);
        User::factory()->create([
            'name' => 'PME Uji Urutan',
            'email' => 'pme-urutan@example.test',
            'role' => User::ROLE_DEWAN,
            'division_id' => $pmeDivision->id,
            'position_id' => $dewanPosition->id,
            'divisi' => $pmeDivision->name,
            'order' => 2,
        ]);
        User::factory()->create([
            'name' => 'Komjakum Uji Urutan',
            'email' => 'komjakum-urutan@example.test',
            'role' => User::ROLE_DEWAN,
            'division_id' => $komjakumDivision->id,
            'position_id' => $dewanPosition->id,
            'divisi' => $komjakumDivision->name,
            'order' => 3,
        ]);
        User::factory()->create([
            'name' => 'Sekretaris Uji Urutan',
            'email' => 'sekretaris-urutan@example.test',
            'role' => User::ROLE_SECRETARIAT,
            'division_id' => $sekretarisDivision->id,
            'position_id' => $sekretarisPosition->id,
            'divisi' => $sekretarisDivision->name,
            'order' => 4,
        ]);

        $response = $this->actingAs($user)->get(route('activities.create'));

        $response->assertOk();

        $orderedGroups = collect($response->viewData('dewanUsers'))
            ->keys()
            ->filter(fn (string $label) => in_array($label, [
                'Ketua DJSN',
                'Komisi PME',
                'Komjakum',
                'Sekretaris DJSN',
            ], true))
            ->values()
            ->all();

        $this->assertSame([
            'Ketua DJSN',
            'Komisi PME',
            'Komjakum',
            'Sekretaris DJSN',
        ], $orderedGroups);
    }

    public function test_internal_pic_labels_follow_master_group_order(): void
    {
        $pmeDivision = Division::query()->updateOrCreate(
            ['name' => 'Komisi PME'],
            [
                'category' => 'Komisi',
                'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'commission_code' => 'pme',
                'is_commission' => true,
                'order' => 1,
            ]
        );
        $komjakumDivision = Division::query()->updateOrCreate(
            ['name' => 'Komjakum'],
            [
                'category' => 'Komisi',
                'structure_group' => Division::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'commission_code' => 'komjakum',
                'is_commission' => true,
                'order' => 2,
            ]
        );
        $sekretarisDivision = Division::query()->updateOrCreate(
            ['name' => 'Sekretaris DJSN'],
            [
                'category' => 'Sekretariat DJSN',
                'structure_group' => Division::STRUCTURE_GROUP_SECRETARY,
                'access_profile' => User::ACCESS_PROFILE_SET_DJSN,
                'order' => 3,
            ]
        );

        $dewanPosition = Position::query()->updateOrCreate(
            ['code' => 'anggota_dewan_urut_pic'],
            [
                'name' => 'Anggota Dewan Urut PIC',
                'structure_group' => User::STRUCTURE_GROUP_DEWAN,
                'access_profile' => User::ACCESS_PROFILE_DEWAN,
                'order' => 1,
                'receives_disposition' => true,
            ]
        );
        $sekretarisPosition = Position::query()->updateOrCreate(
            ['code' => 'sekretaris_djsn_urut_pic'],
            [
                'name' => 'Sekretaris DJSN Urut PIC',
                'structure_group' => User::STRUCTURE_GROUP_SET_DJSN,
                'access_profile' => User::ACCESS_PROFILE_SET_DJSN,
                'order' => 1,
                'receives_disposition' => true,
                'disposition_group_label' => 'Sekretaris DJSN',
            ]
        );

        User::factory()->create([
            'name' => 'PME Uji PIC',
            'email' => 'pme-pic@example.test',
            'role' => User::ROLE_DEWAN,
            'division_id' => $pmeDivision->id,
            'position_id' => $dewanPosition->id,
            'divisi' => $pmeDivision->name,
            'order' => 2,
        ]);
        User::factory()->create([
            'name' => 'Komjakum Uji PIC',
            'email' => 'komjakum-pic@example.test',
            'role' => User::ROLE_DEWAN,
            'division_id' => $komjakumDivision->id,
            'position_id' => $dewanPosition->id,
            'divisi' => $komjakumDivision->name,
            'order' => 3,
        ]);
        User::factory()->create([
            'name' => 'Sekretaris Uji PIC',
            'email' => 'sekretaris-pic@example.test',
            'role' => User::ROLE_SECRETARIAT,
            'division_id' => $sekretarisDivision->id,
            'position_id' => $sekretarisPosition->id,
            'divisi' => $sekretarisDivision->name,
            'order' => 4,
        ]);

        $this->assertSame([
            'Komisi PME',
            'Komjakum',
            'Sekretaris DJSN',
        ], \App\Models\Activity::derivePicGroupsFromDispositionNames([
            'Sekretaris Uji PIC',
            'Komjakum Uji PIC',
            'PME Uji PIC',
        ]));
    }

    private function createAdminUser(): User
    {
        return User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super-admin-urutan@example.test',
            'role' => User::ROLE_SUPER_ADMIN,
            'divisi' => 'Sekretariat DJSN',
        ]);
    }
}
