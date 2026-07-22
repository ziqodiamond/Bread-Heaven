<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil kategori
        $makananInstant = Category::where('name', 'Makanan Innstan')->first();
        $elektronik = Category::where('name', 'Elektronik')->first();
        $roti = Category::where('name', 'Roti')->first();

        $products = [
            [
                'name'        => 'Indomie Ayam Bawang',
                'sku'         => 'INDOMIE-AYAM-001',
                'category_id' => $makananInstant?->id ?? 1,
                'description' => 'Indomie rasa ayam bawang, lezat dan gurih. Sempurna untuk sarapan atau camilan kapan saja.',
                'price'       => 5000,
                'stock'       => 500,
                'weight'      => 80,
                'status'      => 'available',
                'image'       => 'products/indoemie_ayam_bawang.jpg',
            ],
            [
                'name'        => 'SSD SATA 240GB',
                'sku'         => 'SSD-SATA-240-001',
                'category_id' => $elektronik?->id ?? 1,
                'description' => 'Storage SSD SATA 240GB dengan kecepatan transfer tinggi dan reliable untuk kebutuhan computing Anda.',
                'price'       => 700000,
                'stock'       => 50,
                'weight'      => 100,
                'status'      => 'available',
                'image'       => 'products/ssd_sata_240gb.jpg',
            ],
            [
                'name'        => 'Roti Tawar Spesial',
                'sku'         => 'ROTI-TAWAR-SPSL-001',
                'category_id' => $roti?->id ?? 1,
                'description' => 'Roti tawar spesial dengan tekstur lembut dan rasa yang lezat. Dibuat fresh setiap hari menggunakan bahan berkualitas premium.',
                'price'       => 25000,
                'stock'       => 100,
                'weight'      => 500,
                'status'      => 'available',
                'image'       => 'products/SpUN6VgrCp4OJ4wktS3sAhFbvnbMgbgrBAIkGVaq.jpg',
            ],
        ];

        foreach ($products as $index => $productData) {
            $imagePath = $productData['image'];
            unset($productData['image']);

            // Buat produk
            $product = Product::create(array_merge(
                $productData,
                [
                    'slug' => Str::slug($productData['name']),
                ]
            ));

            // Buat product image
            ProductImage::create([
                'product_id' => $product->id,
                'image_url'  => $imagePath,
                'sort_order' => 0,
                'is_primary' => true,
            ]);
        }
    }
}
