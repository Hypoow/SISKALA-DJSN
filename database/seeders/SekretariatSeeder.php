<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SekretariatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing Sekretariat users to prevent duplicates (optional, but safe for seeds)
        User::where('role', 'DJSN')->delete();
        // Also clear old 'Sekretariat' role if exists from previous step
        User::where('role', 'Sekretariat')->delete();

        $members = [
            'Imron Rosadi' => ['role' => 'Sekretaris DJSN', 'order' => 101],
            'Dwi Janatun Rahayu' => ['role' => 'Kepala Bagian Umum', 'order' => 102],
            'Wenny Kartika Ayunungtyas' => ['role' => 'Plt.Kepala Bagian Persidangan', 'order' => 103],
            'Annisa' => ['role' => 'Kepala Sub Tata Usaha dan Rumah Tangga', 'order' => 104],
            'Eko Sudarmawan' => ['role' => 'Kepala Sub Protokol dan Kehumasan', 'order' => 105],
        ];

        foreach ($members as $name => $data) {
            // Construct email: firstname.lastname@djsn.com
            // Ensure lowercase and dot separation
            $emailName = strtolower(str_replace(['.', ' '], ['', '.'], $name)); // simple clean
            // Better cleaning:
            $cleanForEmail = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '.', $name), '.'));
            $email = $cleanForEmail . '@djsn.com';

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => 'DJSN', // Updated Role as requested
                    'divisi' => $data['role'], // Job Title
                    'order' => $data['order'],
                ]
            );
        }
    }
}
