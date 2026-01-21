<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUserOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $order = 999; // Default order

            // Hierarchy Logic
            if ($user->role === 'Dewan') {
                // Dewan Hierarchy
                if (stripos($user->divisi, 'Ketua DJSN') !== false) {
                    $order = 10;
                } elseif (stripos($user->divisi, 'Ketua Komisi 1') !== false) {
                    $order = 20;
                } elseif (stripos($user->divisi, 'Komisi 1') !== false) { // Anggota Komisi 1
                    $order = 21;
                } elseif (stripos($user->divisi, 'Ketua Komisi 2') !== false) {
                    $order = 30;
                } elseif (stripos($user->divisi, 'Komisi 2') !== false) { // Anggota Komisi 2
                    $order = 31;
                } elseif (stripos($user->divisi, 'Ketua Komisi 3') !== false) {
                    $order = 40;
                } elseif (stripos($user->divisi, 'Komisi 3') !== false) { // Anggota Komisi 3
                    $order = 41;
                } else {
                    $order = 50; // Other Dewan
                }
            } else {
                // Set. DJSN / Secretariat Hierarchy
                $order = 100; // Base for Secretariat

                if (stripos($user->divisi, 'Sekretaris DJSN') !== false || stripos($user->divisi, 'Set.DJSN') !== false  || stripos($user->divisi, 'Sek.DJSN') !== false) {
                    $order = 110;
                } elseif (stripos($user->divisi, 'Kepala Bagian Umum') !== false) {
                    $order = 120;
                } elseif (stripos($user->divisi, 'Kepala Bagian Persidangan') !== false) {
                    $order = 130;
                } elseif (stripos($user->divisi, 'Kepala Sub Tata Usaha') !== false) {
                    $order = 140;
                } elseif (stripos($user->divisi, 'Kepala Sub Protokol') !== false) {
                    $order = 150;
                } elseif ($user->role === 'Tata Usaha') {
                    $order = 160;
                } elseif ($user->role === 'Persidangan') {
                    $order = 170;
                } elseif ($user->role === 'Bagian Umum') {
                    $order = 180;
                }
            }

            $user->order = $order;
            $user->save();
        }
    }
}
