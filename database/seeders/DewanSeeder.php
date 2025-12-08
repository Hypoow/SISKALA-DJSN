<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DewanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing Dewan users to prevent duplicates and ensure clean state
        User::where('role', 'Dewan')->delete();

        $councilMembers = [
            // Ketua DJSN
            'Prof. Dr. Ir. R. Nunung Nuryartono, M.Si.' => ['order' => 1, 'divisi' => 'Ketua DJSN'],
            
            // Komisi PME
            'Muttaqien, S.S., M.P.H., A.A.K.' => ['order' => 2, 'divisi' => 'Komisi PME'],
            'Nikodemus Beriman Purba, S.Psi., M.H.' => ['order' => 3, 'divisi' => 'Komisi PME'],
            'Sudarto, S.E., M.B.A., M.Kom., Ph.D., CGEIT., CA.' => ['order' => 4, 'divisi' => 'Komisi PME'],
            'Robben Rico, A.Md., LLAJ., S.H., S.T., M.Si.' => ['order' => 5, 'divisi' => 'Komisi PME'],
            'Dr. dr. Mahesa Paranadipa Maykel, M.H., MARS.' => ['order' => 6, 'divisi' => 'Komisi PME'],
            'Dr.rer.pol. Syamsul Hidayat Pasaribu, S.E., M.Si.' => ['order' => 7, 'divisi' => 'Komisi PME'],
            'Hermansyah, S.H., AK3.' => ['order' => 8, 'divisi' => 'Komisi PME'],
            
            // Komisi Komjakum
            'Drs. Paulus Agung Pambudhi, M.M.' => ['order' => 9, 'divisi' => 'Komisi Komjakum'],
            'dr. H. Agus Taufiqurrohman, M.Kes., Sp.S.' => ['order' => 10, 'divisi' => 'Komisi Komjakum'],
            'Kunta Wibawa Dasa Nugraha, S.E., M.A., Ph.D.' => ['order' => 11, 'divisi' => 'Komisi Komjakum'],
            'Dra. Indah Anggoro Putri, M.Bus.' => ['order' => 12, 'divisi' => 'Komisi Komjakum'],
            'Prof. Dr. Rudi Purwono, S.E., M.SE.' => ['order' => 13, 'divisi' => 'Komisi Komjakum'],
            'Mickael Bobby Hoelman, S.E., M.Si.' => ['order' => 14, 'divisi' => 'Komisi Komjakum'],
            'Royanto Purba, S.T.' => ['order' => 15, 'divisi' => 'Komisi Komjakum']
        ];

        foreach ($councilMembers as $name => $data) {
            // Create email from name: lowercase, remove titles, replace spaces with dots
            // Example: "Prof. Dr. Ir. R. Nunung Nuryartono, M.Si." -> "nunung.nuryartono@djsn.go.id"
            // For simplicity, let's use a simplified email generation or just first.last
            
            // Cleaning the name for email
            $cleanName = preg_replace('/^(Prof\.|Dr\.|Ir\.|Drs\.|Dra\.|H\.|R\.|rer\.pol\.)\s+/', '', $name); // Remove front titles
            $cleanName = preg_replace('/,.+$/', '', $cleanName); // Remove back titles
            // Get first name only
            $parts = explode(' ', $cleanName);
            $firstName = strtolower($parts[0]);
            
            $email = $firstName . '@djsn.com';

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'), // Default password
                    'role' => 'Dewan',
                    'divisi' => $data['divisi'],
                    'order' => $data['order'],
                ]
            );
        }
    }
}
