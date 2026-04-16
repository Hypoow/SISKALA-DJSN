<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Division;

class MigrateDivisionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Initial categorization logic
        $users = User::all();
        $divisionsMap = [];

        foreach ($users as $user) {
            $divisiName = $user->divisi ?? '-';
            
            // Skip if already has division_id
            if ($user->division_id) continue;

            // Determine Category
            $category = 'Sekretariat DJSN'; // Default
            $normalized = strtoupper($divisiName);

            if (str_contains($normalized, 'KETUA DJSN')) {
                $category = 'Ketua DJSN';
            } elseif ($user->role === 'Dewan' || str_contains($normalized, 'KOMISI') || str_contains($normalized, 'KOMJAKUM') || str_contains($normalized, 'PME')) {
                $category = 'Komisi';
            }

            // Get or Create Division
            $key = $category . '|' . $divisiName;
            if (!isset($divisionsMap[$key])) {
                $division = Division::firstOrCreate([
                    'name' => $divisiName,
                    'category' => $category,
                ]);
                $divisionsMap[$key] = $division->id;
            }

            $user->update(['division_id' => $divisionsMap[$key]]);
        }
        
        // 2. Add some defaults if not present
        $defaults = [
            ['name' => 'Ketua DJSN', 'category' => 'Ketua DJSN', 'order' => 1],
            ['name' => 'Komisi PME', 'category' => 'Komisi', 'order' => 2],
            ['name' => 'KOMJAKUM', 'category' => 'Komisi', 'order' => 3],
            ['name' => 'Sekretaris DJSN', 'category' => 'Sekretariat DJSN', 'order' => 10],
            ['name' => 'Kepala Bagian Tata Usaha', 'category' => 'Sekretariat DJSN', 'order' => 11],
        ];

        foreach ($defaults as $d) {
            Division::firstOrCreate(['name' => $d['name']], $d);
        }
    }
}
