<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Voucher;

class TestFactoriesSeeder extends Seeder
{
    public function run(): void
    {
        echo "\nCreating test data using factories...\n\n";

        echo "1. Creating 5 Categories...\n";
        $categories = Category::factory()->count(5)->create();
        echo "✓ Created " . $categories->count() . " categories\n";
        foreach ($categories as $cat) {
            echo "  - {$cat->name} (ID: {$cat->id})\n";
        }

        echo "\n2. Creating 10 Products...\n";
        $products = Product::factory()->count(10)->create();
        echo "✓ Created " . $products->count() . " products\n";
        foreach ($products as $prod) {
            echo "  - {$prod->name} (SKU: {$prod->sku})\n";
        }

        echo "\n3. Creating 5 Vouchers...\n";
        $vouchers = Voucher::factory()->count(5)->create();
        echo "✓ Created " . $vouchers->count() . " vouchers\n";
        foreach ($vouchers as $v) {
            echo "  - {$v->code} ({$v->type} - Rp {$v->value})\n";
        }

        echo "\n4. Attaching products to vouchers...\n";
        foreach ($vouchers as $voucher) {
            $randomProducts = $products->random(rand(2, 5));
            foreach ($randomProducts as $product) {
                $voucher->products()->attach($product->id, ['is_excluded' => false]);
            }
            echo "  - {$voucher->code}: " . $randomProducts->count() . " products attached\n";
        }

        echo "\n=== FINAL SUMMARY ===\n";
        echo "Total Categories: " . Category::count() . "\n";
        echo "Total Products: " . Product::count() . "\n";
        echo "Total Vouchers: " . Voucher::count() . "\n";
        echo "Voucher-Product relationships: " . \DB::table('voucher_products')->count() . "\n";
    }
}
