<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create TA Users
        $taUsers = [
            ['name' => 'TA Komisi PME Test', 'email' => 'ta.pme@djsn.com', 'role' => 'TA', 'divisi' => 'Komisi PME'],
            ['name' => 'TA Komjakum Test', 'email' => 'ta.komjakum@djsn.com', 'role' => 'TA', 'divisi' => 'Komjakum'],
        ];

        foreach ($taUsers as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'divisi' => $user['divisi'],
                    'order' => 99 // Default high order
                ]
            );
        }

        // 2. Create/Update Persidangan Users to have 'Divisi'
        // Let's create specific ones for testing
        $persidanganUsers = [
            ['name' => 'Staf Persidangan PME', 'email' => 'persidangan.pme@djsn.com', 'role' => 'Persidangan', 'divisi' => 'Komisi PME'],
            ['name' => 'Staf Persidangan Komjakum', 'email' => 'persidangan.komjakum@djsn.com', 'role' => 'Persidangan', 'divisi' => 'Komjakum'],
            // Generic Persidangan
            ['name' => 'Staf Persidangan Umum', 'email' => 'persidangan.umum@djsn.com', 'role' => 'Persidangan', 'divisi' => 'Sekretariat DJSN'],
        ];

        foreach ($persidanganUsers as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'divisi' => $user['divisi'],
                    'order' => 98
                ]
            );
        }
    }
}
