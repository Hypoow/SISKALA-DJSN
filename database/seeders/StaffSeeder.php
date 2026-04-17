<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staffGroups = [
            'sekretariat' => [
                'Dwi Janatun Rahayu',
                'Wenny Kartika Ayunungtyas',
                'Annisa',
                'Eko Sudarmawan',
                'Budi Mulyono',
                'Fatoni Mahendra Kurniawan',
                'Athi Rahmawati',
                'Eka Yeyen Pertiwi',
                'Azzahra',
                'Iis Okta Nurria Putri',
                'Rahmi Syafina',
                'Sarah Puspita Rahmasari',
                'Nabila Ika Putri',
                'Qafyl Ramadhano Mufti',
                'Zahra Ainussyifa',
                'Addin Rama Adzani Salim',
                'Muhammad Tazqiatun Nafs',
                'Fadilah Cahyani',
                'Sulistia Sari',
                'Dimas Bayuaji Santoso Putera',
                'Edlyn Oktamalia',
                'Garda Anggara',
                'Selvia Rustyani',
                'Celsis Delania Zebua',
                'Farasya',
            ],
            'ta' => [
                'Winda Sari',
                'Muhammad Iqbal Khatami',
                'Sandro Andriawan',
                'Fahmi Hakam',
                'Mukhlisah',
                'Khairunnas',
                'Lutfi Aulia Ulfah',
                'Anindhia Salsabila',
                'Averin Dian Boruna Sidauruk',
                "Sa'dan Mubarok",
            ],
        ];

        foreach ($staffGroups as $type => $names) {
            foreach ($names as $name) {
                Staff::query()->firstOrCreate([
                    'name' => $name,
                    'type' => $type,
                ]);
            }
        }
    }
}
