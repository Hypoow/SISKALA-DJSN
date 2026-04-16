<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Division;
use App\Models\User;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class ActivityVisualizationGroupingTest extends TestCase
{
    public function test_normalize_commission_display_label_merges_role_based_titles(): void
    {
        $this->assertSame(
            'Komisi PME',
            Activity::normalizeCommissionDisplayLabel('Ketua Komisi PME', 'Dewan', 'Komisi')
        );

        $this->assertSame(
            'Komisi Komjakum',
            Activity::normalizeCommissionDisplayLabel('Anggota Komjakum', 'Dewan', 'Komisi')
        );

        $this->assertSame(
            'Komisi Komjakum',
            Activity::normalizeCommissionDisplayLabel('Komisi Komjakum', 'Dewan', 'Komisi')
        );
    }

    public function test_build_visualization_groups_keeps_disposition_order_inside_each_group(): void
    {
        $usersMap = new Collection([
            'Indah Anggoro Putri' => $this->makeUser('Indah Anggoro Putri', 'Dewan', 'Ketua DJSN', 'Ketua DJSN'),
            'Royanto Purba' => $this->makeUser('Royanto Purba', 'Dewan', 'Ketua Komjakum', 'Komisi'),
            'Mickael Bobby Hoelman' => $this->makeUser('Mickael Bobby Hoelman', 'Dewan', 'Anggota Komjakum', 'Komisi'),
            'Nikodemus Beriman Purba' => $this->makeUser('Nikodemus Beriman Purba', 'Dewan', 'Ketua Komisi PME', 'Komisi'),
            'Robben Rico' => $this->makeUser('Robben Rico', 'Dewan', 'Anggota Komisi PME', 'Komisi'),
            'Imron Rosadi' => $this->makeUser('Imron Rosadi', 'DJSN', 'Sekretariat DJSN', 'Sekretariat DJSN'),
        ]);

        $groups = Activity::buildVisualizationGroupsFromDisposition([
            'Royanto Purba',
            'Mickael Bobby Hoelman',
            'Nikodemus Beriman Purba',
            'Robben Rico',
            'Indah Anggoro Putri',
            'Imron Rosadi',
        ], $usersMap);

        $this->assertSame(
            ['Ketua DJSN', 'Komisi Komjakum', 'Komisi PME', 'Sekretariat DJSN'],
            $groups->pluck('label')->all()
        );

        $this->assertSame(['Indah Anggoro Putri'], $groups->get(0)['members']);
        $this->assertSame(['Royanto Purba', 'Mickael Bobby Hoelman'], $groups->get(1)['members']);
        $this->assertSame(['Nikodemus Beriman Purba', 'Robben Rico'], $groups->get(2)['members']);
        $this->assertSame(['Imron Rosadi'], $groups->get(3)['members']);
    }

    private function makeUser(string $name, string $role, string $divisionName, string $category): User
    {
        $user = new User([
            'name' => $name,
            'role' => $role,
            'divisi' => $divisionName,
        ]);

        $user->setRelation('division', new Division([
            'name' => $divisionName,
            'category' => $category,
        ]));

        return $user;
    }
}
