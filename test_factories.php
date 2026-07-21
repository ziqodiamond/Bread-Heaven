#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

// Load Composer's autoloader...
require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel and get the container
$app = require_once __DIR__ . '/bootstrap/app.php';

// Make sure the application is booted
$app->boot();

echo "Creating test data using factories...\n\n";

echo "1. Creating 5 Categories...\n";
$categories = \App\Models\Category::factory()->count(5)->create();
echo "✓ Created " . $categories->count() . " categories\n";
foreach ($categories as $cat) {
    echo "  - {$cat->name} (ID: {$cat->id})\n";
}

echo "\n2. Creating 10 Products...\n";
$products = \App\Models\Product::factory()->count(10)->create();
echo "✓ Created " . $products->count() . " products\n";
foreach ($products as $prod) {
    echo "  - {$prod->name} (SKU: {$prod->sku})\n";
}

echo "\n3. Creating 5 Vouchers...\n";
$vouchers = \App\Models\Voucher::factory()->count(5)->create();
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
echo "Total Categories: " . \App\Models\Category::count() . "\n";
echo "Total Products: " . \App\Models\Product::count() . "\n";
echo "Total Vouchers: " . \App\Models\Voucher::count() . "\n";
echo "Voucher-Product relationships: " . \Illuminate\Support\Facades\DB::table('voucher_products')->count() . "\n";
