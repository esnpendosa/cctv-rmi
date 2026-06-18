<?php

namespace Database\Seeders;

use App\Models\CameraCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Class CategorySeeder
 * 
 * Seeds default CCTV categories.
 * 
 * @package Database\Seeders
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Jalan Raya',
                'color' => '#3b82f6', // blue
                'icon' => 'bi-signpost-split',
                'description' => 'Kamera pengawas area lalu lintas jalan raya dan simpang jalan.',
            ],
            [
                'name' => 'Gedung',
                'color' => '#10b981', // green
                'icon' => 'bi-building',
                'description' => 'Kamera pengawas area sekitar gedung dan fasad luar.',
            ],
            [
                'name' => 'Parkir',
                'color' => '#f59e0b', // amber/orange
                'icon' => 'bi-p-circle',
                'description' => 'Kamera pengawas area parkir mobil dan motor.',
            ],
            [
                'name' => 'Lobby',
                'color' => '#8b5cf6', // purple
                'icon' => 'bi-door-open',
                'description' => 'Kamera pengawas area lobby utama dan meja resepsionis.',
            ],
            [
                'name' => 'Outdoor',
                'color' => '#ef4444', // red
                'icon' => 'bi-tree',
                'description' => 'Kamera pengawas area terbuka hijau, pagar pembatas, dan halaman.',
            ],
            [
                'name' => 'Indoor',
                'color' => '#6366f1', // indigo
                'icon' => 'bi-house',
                'description' => 'Kamera pengawas koridor dalam, ruang kerja, dan ruang rapat.',
            ],
        ];

        foreach ($categories as $cat) {
            CameraCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'color' => $cat['color'],
                    'icon' => $cat['icon'],
                    'description' => $cat['description'],
                ]
            );
        }
    }
}
