<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin (TU)
        User::updateOrCreate(
            ['email' => 'admin@djsn.go.id'],
            [
                'name' => 'Admin TU',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'divisi' => 'TU',
            ]
        );

        // User (Other Division)
        User::updateOrCreate(
            ['email' => 'user@djsn.go.id'],
            [
                'name' => 'User Divisi',
                'password' => Hash::make('password'),
                'role' => 'user',
                'divisi' => 'Umum',
            ]
        );
    }
}
