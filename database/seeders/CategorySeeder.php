<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Roti',
                'description' => 'Koleksi roti tawar berkualitas tinggi',
                'icon' => '🍞',
                'sort_order' => 1,
            ],
            [
                'name' => 'Kue',
                'description' => 'Berbagai macam kue lezat',
                'icon' => '🎂',
                'sort_order' => 2,
            ],
            [
                'name' => 'Pastry',
                'description' => 'Pastry dan pie premium',
                'icon' => '🥐',
                'sort_order' => 3,
            ],
            [
                'name' => 'Snack',
                'description' => 'Camilan ringan dan gurih',
                'icon' => '🍪',
                'sort_order' => 5,
            ],
            [
                'name' => 'Kue Kering',
                'description' => 'Kue kering untuk oleh-oleh',
                'icon' => '🧁',
                'sort_order' => 6,
            ],
            [
                'name' => 'Makanan Innstan',
                'description' => '',
                'icon' => '',
                'sort_order' => 7,
            ],
            [
                'name' => 'Elektronik',
                'description' => '',
                'icon' => '',
                'sort_order' => 8,
            ],

        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'icon' => $category['icon'],
                'sort_order' => $category['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
