<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            ['name' => 'PP ALMA JKN', 'color' => '#FFC0CB'], // Pink
            ['name' => 'PP ALMA P2SK', 'color' => '#FFD700'], // Yellow
            ['name' => 'RDP', 'color' => '#90EE90'], // Light Green
            ['name' => 'ICK', 'color' => '#28a745'], // Green
            ['name' => 'SOTK', 'color' => '#E6E6FA'], // Lavender
            ['name' => 'RPP JKK JKm', 'color' => '#D3D3D3'], // Light Gray
            ['name' => 'Program Perumahan', 'color' => '#F0E68C'], // Khaki
            ['name' => 'RENSTRA', 'color' => '#198754'], // Dark Green
            ['name' => 'KAJIAN', 'color' => '#E6E6FA'], // Lavender
            ['name' => 'Ad Hoc PMI', 'color' => '#FFDAB9'], // Peach
            ['name' => 'SNP', 'color' => '#B0C4DE'], // Light Steel Blue
            ['name' => 'PAW', 'color' => '#FFC0CB'], // Pink (Pale)
            ['name' => 'AKUMANTAP', 'color' => '#008080'], // Teal/Dark Cyan
            ['name' => 'TAPERA', 'color' => '#E6E6FA'], // Lavender
            ['name' => 'POKJA 1', 'color' => '#C1E1C1'], // Pale Green
            ['name' => 'RKAT', 'color' => '#6495ED'], // Cornflower Blue
            ['name' => 'PLENO', 'color' => '#DC143C'], // Crimson
            ['name' => 'ILAS/ PJP Lansia', 'color' => '#D3D3D3'], // Gray
            ['name' => 'PERSI', 'color' => '#ADD8E6'], // Light Blue
            ['name' => 'RTM', 'color' => '#C1E1C1'], // Pale Green
            ['name' => 'INTERNAL', 'color' => '#B0E0E6'], // Powder Blue
            ['name' => 'EKSTERNAL', 'color' => '#800080'], // Purple
        ];

        foreach ($topics as $topic) {
            \App\Models\Topic::firstOrCreate(['name' => $topic['name']], $topic);
        }
    }
}
