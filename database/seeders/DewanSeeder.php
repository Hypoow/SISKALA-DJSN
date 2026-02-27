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
            'Nunung Nuryartono' => ['order' => 1, 'divisi' => 'Ketua DJSN'],
            
            // Komisi Pengawasan Monitoring dan Evaluasi (PME)
            'Muttaqien' => ['order' => 2, 'divisi' => 'Komisi PME'],
            'Nikodemus Beriman Purba' => ['order' => 3, 'divisi' => 'Komisi PME'],
            'Sudarto' => ['order' => 4, 'divisi' => 'Komisi PME'],
            'Robben Rico' => ['order' => 5, 'divisi' => 'Komisi PME'],
            'Mahesa Paranadipa Maykel' => ['order' => 6, 'divisi' => 'Komisi PME'],
            'Syamsul Hidayat Pasaribu' => ['order' => 7, 'divisi' => 'Komisi PME'],
            'Hermansyah' => ['order' => 8, 'divisi' => 'Komisi PME'],
            
            // Komisi Kebijakan Umum (Komjakum)
            'Paulus Agung Pambudhi' => ['order' => 9, 'divisi' => 'Komjakum'],
            'Agus Taufiqurrohman' => ['order' => 10, 'divisi' => 'Komjakum'],
            'Kunta Wibawa Dasa Nugraha' => ['order' => 11, 'divisi' => 'Komjakum'],
            'Indah Anggoro Putri' => ['order' => 12, 'divisi' => 'Komjakum'],
            'Rudi Purwono' => ['order' => 13, 'divisi' => 'Komjakum'],
            'Mickael Bobby Hoelman' => ['order' => 14, 'divisi' => 'Komjakum'],
            'Royanto Purba' => ['order' => 15, 'divisi' => 'Komjakum']
        ];

        foreach ($councilMembers as $name => $data) {
            // Remove titles to get the base name for email
            // Pattern matches common titles and honorifics followed by dot and optional space, repeated at the start
            $cleanName = preg_replace('/^((Prof|Dr|Ir|Drs|Dra|H|R|rer\.pol)\.\s*)+/', '', $name); 
            
            // Remove trailing titles starting with comma
            $cleanName = preg_replace('/,.+$/', '', $cleanName);
            
            // Trim whitespace
            $cleanName = trim($cleanName);
            
            // Construct email: firstname.lastname@djsn.com
            $emailName = strtolower(str_replace(' ', '.', $cleanName));
            $email = $emailName . '@djsn.com';

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => 'Dewan',
                    'divisi' => $data['divisi'],
                    'order' => $data['order'],
                ]
            );
        }
    }
}
