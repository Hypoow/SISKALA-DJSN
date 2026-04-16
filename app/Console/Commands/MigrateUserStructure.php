<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateUserStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-user-structure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old user divisions into positions and unit_kerja structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Default Unit Kerjas
        $units = [
            ['name' => 'Sekretariat DJSN', 'category' => 'Sekretariat DJSN'],
            ['name' => 'Komisi Kebijakan Umum (KOMJAKUM)', 'category' => 'Komisi'],
            ['name' => 'Komisi Pemantauan, Monitoring, dan Evaluasi (PME)', 'category' => 'Komisi'],
            ['name' => 'Persidangan', 'category' => 'Sekretariat DJSN'],
            ['name' => 'Protokol & Humas', 'category' => 'Sekretariat DJSN'],
            ['name' => 'Keuangan', 'category' => 'Sekretariat DJSN'],
            ['name' => 'Tata Usaha dan Rumah Tangga', 'category' => 'Sekretariat DJSN'],
            ['name' => 'Bagian Umum', 'category' => 'Sekretariat DJSN'],
            ['name' => 'Ketua DJSN', 'category' => 'Ketua DJSN'],
        ];

        foreach ($units as $index => $u) {
            \App\Models\Division::firstOrCreate(
                ['name' => $u['name']],
                ['category' => $u['category'], 'order' => $index + 1]
            );
        }

        // Default Positions
        $positions = [
            ['name' => 'Sekretaris DJSN', 'code' => 'sekretaris_djsn', 'receives_disposition' => true, 'disposition_group_label' => 'Sekretariat DJSN', 'report_target_label' => 'Sekretaris DJSN'],
            ['name' => 'Kepala Bagian Umum', 'code' => 'kabag_umum', 'receives_disposition' => true, 'disposition_group_label' => 'Sekretariat DJSN', 'report_target_label' => 'Kepala Bagian Umum'],
            ['name' => 'Kepala Sub. Bag. TU & Rumah Tangga', 'code' => 'kasubag_tu_rt', 'receives_disposition' => true, 'disposition_group_label' => 'Sekretariat DJSN', 'report_target_label' => 'Kepala Sub. Bag. TU & Rumah Tangga'],
            ['name' => 'Plt/Kabag Persidangan', 'code' => 'kabag_persidangan', 'receives_disposition' => true, 'disposition_group_label' => 'Sekretariat DJSN', 'report_target_label' => 'Plt/Kabag Persidangan'],
            ['name' => 'Kepala Sub. Bag. Protokol & Humas', 'code' => 'kasubag_protokol_humas', 'receives_disposition' => true, 'disposition_group_label' => 'Sekretariat DJSN', 'report_target_label' => 'Kepala Sub. Bag. Protokol & Humas'],
            ['name' => 'Staf Tata Usaha', 'code' => 'staf_tu'],
            ['name' => 'Staf Protokol & Humas', 'code' => 'staf_protokol_humas'],
            ['name' => 'Staf Persidangan', 'code' => 'staf_persidangan'],
            ['name' => 'Staf Keuangan', 'code' => 'staf_keuangan'],
            ['name' => 'Tenaga Ahli (TA)', 'code' => 'tenaga_ahli'],
            ['name' => 'Anggota Dewan', 'code' => 'anggota_dewan'],
            ['name' => 'Ketua', 'code' => 'ketua'],
        ];

        foreach ($positions as $index => $p) {
            \App\Models\Position::updateOrCreate(
                ['code' => $p['code']],
                [
                    'name' => $p['name'],
                    'order' => $index + 1,
                    'receives_disposition' => $p['receives_disposition'] ?? null,
                    'disposition_group_label' => $p['disposition_group_label'] ?? null,
                    'report_target_label' => $p['report_target_label'] ?? null,
                ]
            );
        }

        // Migrate Users
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            if ($user->isSuperAdmin()) continue;

            $divString = mb_strtoupper(trim((string) $user->divisi));
            $mappedPositionCode = null;
            $mappedDivisionName = null;

            // Guess position based on their old text strings
            if (str_contains($divString, 'SEKRETARIS DJSN')) {
                $mappedPositionCode = 'sekretaris_djsn';
                $mappedDivisionName = 'Sekretariat DJSN';
            } elseif (str_contains($divString, 'KEPALA BAGIAN UMUM')) {
                $mappedPositionCode = 'kabag_umum';
                $mappedDivisionName = 'Bagian Umum';
            } elseif (str_contains($divString, 'TATA USAHA DAN RUMAH TANGGA') && str_contains($divString, 'SUB')) {
                $mappedPositionCode = 'kasubag_tu_rt';
                $mappedDivisionName = 'Tata Usaha dan Rumah Tangga';
            } elseif (str_contains($divString, 'PROTOKOL DAN KEHUMASAN') && str_contains($divString, 'SUB')) {
                $mappedPositionCode = 'kasubag_protokol_humas';
                $mappedDivisionName = 'Protokol & Humas';
            } elseif ($user->hasRole('Persidangan') && str_contains($divString, 'KEPALA')) {
                $mappedPositionCode = 'kabag_persidangan';
                $mappedDivisionName = 'Persidangan';
            } elseif ($user->hasRole('Persidangan') && str_contains($divString, 'PLT')) {
                $mappedPositionCode = 'kabag_persidangan';
                $mappedDivisionName = 'Persidangan';
            } elseif ($user->hasRole('TA')) {
                $mappedPositionCode = 'tenaga_ahli';
                if (str_contains($divString, 'PME')) $mappedDivisionName = 'Komisi Pemantauan, Monitoring, dan Evaluasi (PME)';
                elseif (str_contains($divString, 'KOMJAKUM')) $mappedDivisionName = 'Komisi Kebijakan Umum (KOMJAKUM)';
            } elseif ($user->hasRole('Dewan')) {
                if (str_contains($divString, 'KETUA DJSN')) {
                    $mappedPositionCode = 'ketua';
                    $mappedDivisionName = 'Ketua DJSN';
                } else {
                    $mappedPositionCode = 'anggota_dewan';
                    if (str_contains($divString, 'PME')) $mappedDivisionName = 'Komisi Pemantauan, Monitoring, dan Evaluasi (PME)';
                    elseif (str_contains($divString, 'KOMJAKUM')) $mappedDivisionName = 'Komisi Kebijakan Umum (KOMJAKUM)';
                }
            } elseif ($user->hasRole('Keuangan')) {
                $mappedPositionCode = 'staf_keuangan';
                $mappedDivisionName = 'Keuangan';
            } elseif ($user->hasRole('Tata Usaha')) {
                $mappedPositionCode = 'staf_tu';
                $mappedDivisionName = 'Tata Usaha dan Rumah Tangga';
            } elseif ($user->hasRole('Persidangan')) {
                $mappedPositionCode = 'staf_persidangan';
                $mappedDivisionName = 'Persidangan';
            } elseif (str_contains($divString, 'PROTOKOL')) {
                $mappedPositionCode = 'staf_protokol_humas';
                $mappedDivisionName = 'Protokol & Humas';
            }

            // Assign Position
            if ($mappedPositionCode) {
                $pos = \App\Models\Position::where('code', $mappedPositionCode)->first();
                if ($pos) {
                    $user->position_id = $pos->id;
                }
            }

            // Assign Unit Kerja
            if ($mappedDivisionName) {
                $div = \App\Models\Division::where('name', $mappedDivisionName)->first();
                if ($div) {
                    $user->division_id = $div->id;
                    $user->divisi = $div->name; // legacy compatibility mapping
                }
            }

            $user->save();
        }

        // Clean up redundant divisions (optional, but let's just leave string divisions untouched that aren't matching our target set, 
        // to avoid breaking things too quickly. They can delete the unused ones in the UI).
        $this->info('Migration of user structure complete.');
    }
}
