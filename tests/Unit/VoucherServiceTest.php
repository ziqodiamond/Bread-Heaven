<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoucherServiceTest extends TestCase
{
    use RefreshDatabase;

    private VoucherService $voucherService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->voucherService = new VoucherService();
    }

    /**
     * Test: Voucher berlaku untuk semua produk ketika tidak ada rules
     */
    public function test_voucher_applies_to_all_products_when_no_rules(): void
    {
        $voucher = Voucher::factory()->create([
            'is_active' => true,
            'status' => 'active',
            'type' => 'fixed',
            'value' => 10000,
        ]);

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        // Test eligibility
        $this->assertTrue(
            $this->voucherService->isProductEligibleForVoucher($voucher, $product1->id)
        );
        $this->assertTrue(
            $this->voucherService->isProductEligibleForVoucher($voucher, $product2->id)
        );
    }

    /**
     * Test: Voucher hanya berlaku untuk produk yang di-include
     */
    public function test_voucher_applies_only_to_included_products(): void
    {
        $voucher = Voucher::factory()->create();
        $includedProduct = Product::factory()->create();
        $excludedProduct = Product::factory()->create();

        // Attach included product
        $voucher->products()->attach($includedProduct->id, ['is_excluded' => false]);

        $this->assertTrue(
            $this->voucherService->isProductEligibleForVoucher($voucher, $includedProduct->id)
        );
        
        // Product yang tidak di-include tidak eligible
        $this->assertFalse(
            $this->voucherService->isProductEligibleForVoucher($voucher, $excludedProduct->id)
        );
    }

    /**
     * Test: Excluded products tidak eligible meski ada di included list
     */
    public function test_excluded_products_override_included_products(): void
    {
        $voucher = Voucher::factory()->create();
        $product = Product::factory()->create();

        $voucher->products()->attach($product->id, ['is_excluded' => false]);
        $voucher->products()->attach($product->id, ['is_excluded' => true]);

        // Product yang di-exclude tidak eligible
        $this->assertFalse(
            $this->voucherService->isProductEligibleForVoucher($voucher, $product->id)
        );
    }

    /**
     * Test: validateProducts hanya fail jika SEMUA produk tidak eligible
     * ✅ FIXED LOGIC: Sebelumnya fail jika ada 1 produk saja tidak eligible
     */
    public function test_validate_products_fails_only_if_all_products_ineligible(): void
    {
        $voucher = Voucher::factory()->create([
            'is_active' => true,
            'status' => 'active',
            'allow_on_flash_sale' => true,
            'allow_on_discount' => true,
        ]);

        $eligibleProduct = Product::factory()->create();
        $ineligibleProduct = Product::factory()->create();

        $voucher->products()->attach($eligibleProduct->id, ['is_excluded' => false]);

        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        // Case 1: Cart dengan 1 eligible product - HARUS PASS
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $eligibleProduct->id,
        ]);

        $cart->refresh();
        $this->assertTrue(
            $this->voucherService->validateProducts($voucher, $cart->items)
        );

        // Case 2: Cart dengan mixed eligible dan ineligible - HARUS PASS
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $ineligibleProduct->id,
        ]);

        $cart->refresh();
        $this->assertTrue(
            $this->voucherService->validateProducts($voucher, $cart->items)
        );

        // Case 3: Cart dengan hanya ineligible products - HARUS FAIL
        $cart->items()->delete();
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $ineligibleProduct->id,
        ]);

        $cart->refresh();
        $this->assertFalse(
            $this->voucherService->validateProducts($voucher, $cart->items)
        );
    }

    /**
     * Test: validateCategories dengan multiple categories
     */
    public function test_validate_categories_with_mixed_categories(): void
    {
        $voucher = Voucher::factory()->create([
            'is_active' => true,
            'status' => 'active',
            'allow_on_flash_sale' => true,
            'allow_on_discount' => true,
        ]);

        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $product1 = Product::factory()->create(['category_id' => $category1->id]);
        $product2 = Product::factory()->create(['category_id' => $category2->id]);

        $voucher->categories()->attach($category1->id, ['is_excluded' => false]);

        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        // Cart dengan 1 eligible category - HARUS PASS
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
        ]);

        $cart->refresh();
        $this->assertTrue(
            $this->voucherService->validateCategories($voucher, $cart->items)
        );

        // Cart dengan mixed categories - HARUS PASS (ada 1 eligible)
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
        ]);

        $cart->refresh();
        $this->assertTrue(
            $this->voucherService->validateCategories($voucher, $cart->items)
        );

        // Cart dengan hanya ineligible category - HARUS FAIL
        $cart->items()->delete();
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
        ]);

        $cart->refresh();
        $this->assertFalse(
            $this->voucherService->validateCategories($voucher, $cart->items)
        );
    }

    /**
     * Test: validateProducts dengan no rules berlaku untuk semua
     */
    public function test_validate_products_with_no_rules_applies_to_all(): void
    {
        $voucher = Voucher::factory()->create([
            'is_active' => true,
            'status' => 'active',
            'allow_on_flash_sale' => true,
            'allow_on_discount' => true,
        ]);

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
        ]);

        $cart->refresh();

        // Tanpa rules, semua produk eligible - HARUS PASS
        $this->assertTrue(
            $this->voucherService->validateProducts($voucher, $cart->items)
        );
    }

    /**
     * Test: getEligibleProductIds returns correct list
     */
    public function test_get_eligible_product_ids(): void
    {
        $voucher = Voucher::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();

        $voucher->products()->attach($product1->id, ['is_excluded' => false]);
        $voucher->products()->attach($product2->id, ['is_excluded' => false]);
        $voucher->products()->attach($product3->id, ['is_excluded' => true]);

        $eligible = $this->voucherService->getEligibleProductIds($voucher);

        $this->assertContains($product1->id, $eligible);
        $this->assertContains($product2->id, $eligible);
        $this->assertNotContains($product3->id, $eligible);
    }

    /**
     * Test: getEligibleProductIds returns empty for no rules
     */
    public function test_get_eligible_product_ids_empty_for_no_rules(): void
    {
        $voucher = Voucher::factory()->create();

        $eligible = $this->voucherService->getEligibleProductIds($voucher);

        $this->assertEmpty($eligible);
    }

    /**
     * Test: validateMinimumPurchase
     */
    public function test_validate_minimum_purchase(): void
    {
        $voucher = Voucher::factory()->create([
            'minimum_purchase' => 100000,
        ]);

        $this->assertFalse(
            $this->voucherService->validateMinimumPurchase($voucher, 50000)
        );

        $this->assertTrue(
            $this->voucherService->validateMinimumPurchase($voucher, 100000)
        );

        $this->assertTrue(
            $this->voucherService->validateMinimumPurchase($voucher, 150000)
        );
    }

    /**
     * Test: validateQuota
     */
    public function test_validate_quota(): void
    {
        $voucher = Voucher::factory()->create([
            'quota' => 10,
            'used_count' => 0,
        ]);

        $this->assertTrue(
            $this->voucherService->validateQuota($voucher)
        );

        $voucher->update(['used_count' => 9]);
        $this->assertTrue(
            $this->voucherService->validateQuota($voucher)
        );

        $voucher->update(['used_count' => 10]);
        $this->assertFalse(
            $this->voucherService->validateQuota($voucher)
        );
    }

    /**
     * Test: validateUser for members only
     */
    public function test_validate_user_members_only(): void
    {
        $voucher = Voucher::factory()->create([
            'members_only' => true,
        ]);

        $user = User::factory()->create();

        $this->assertTrue(
            $this->voucherService->validateUser($voucher, $user)
        );

        $this->assertFalse(
            $this->voucherService->validateUser($voucher, null)
        );
    }

    /**
     * Test: validateUser for public voucher
     */
    public function test_validate_user_public(): void
    {
        $voucher = Voucher::factory()->create([
            'members_only' => false,
        ]);

        $this->assertTrue(
            $this->voucherService->validateUser($voucher, null)
        );

        $user = User::factory()->create();
        $this->assertTrue(
            $this->voucherService->validateUser($voucher, $user)
        );
    }
}
