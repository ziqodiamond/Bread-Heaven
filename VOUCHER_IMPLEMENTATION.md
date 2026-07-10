# Dokumentasi Implementasi Voucher dengan Kombinasi

## Overview
Fitur voucher yang diperbaharui dengan dukungan kombinasi multiple vouchers (max 2) dengan logic yang ketat untuk memastikan integritas sistem pembayaran.

## Features

### 1. Multiple Vouchers (Max 2)
- User dapat mengaplikasikan maksimal 2 voucher per cart
- Real-time validation dan error messaging
- UI menampilkan voucher yang sudah diaplikasikan dengan tombol remove

### 2. Voucher Combination Rules
- **Type Checking**: Tidak boleh kombinasi 2x diskon atau 2x free_shipping
- **Explicit Rules**: Database table `voucher_combinations` untuk define allowed combinations
- **Both Must Be Combinable**: Voucher harus memiliki `is_combinable = true`

Rules yang diterapkan:
```
- Shipping Type: free_shipping (ongkir gratis)
- Discount Type: fixed atau percent (diskon)
- Rule: 1 shipping + 1 discount = allowed (jika kedua combinable=true dan explicit rule exists)
```

### 3. Quota & Usage Tracking
- Setiap voucher memiliki `quota` dan `used_count`
- Pengecekan real-time apakah quota masih tersedia
- Pencatatan penggunaan di table `voucher_usages` dengan snapshot lengkap

### 4. Voucher Display (Cart)
- **Tab/Carousel**: Daftar voucher tersedia dalam carousel scrollable
- **Copy Button**: Tombol untuk copy kode voucher ke input
- **Applied Vouchers**: Tampilan voucher yang sudah diterapkan dengan breakdown diskon
- **Input Form**: Input field untuk memasukkan kode voucher manual

### 5. Calculation Accuracy
- Diskon dihitung per voucher secara terpisah
- Total diskon = sum dari semua voucher
- Shipping discount ditampilkan terpisah untuk transparency
- Grand total = subtotal - total_discount - shipping_discount + shipping_cost + service_fee

## Database Schema

### Migration: 2026_07_10_200000_update_vouchers_table_for_combinations.php

#### Table: vouchers (modifications)
```sql
- is_combinable: boolean (default: false)
- combination_type: enum['shipping', 'discount', 'both'] (default: 'both')
```

#### New Table: voucher_combinations
```sql
- id: uuid (PK)
- voucher_a_id: uuid (FK to vouchers)
- voucher_b_id: uuid (FK to vouchers)
- is_allowed: boolean (default: true)
- rule_description: string (optional)
- unique: (voucher_a_id, voucher_b_id)
```

#### Table: carts (modifications)
```sql
- vouchers: json array (applied vouchers with breakdown)
- total_discount_amount: integer
- total_shipping_discount: integer
```

#### Table: orders (modifications)
```sql
- vouchers: json array (snapshot of applied vouchers)
- total_discount_amount: integer
- total_shipping_discount: integer
```

## Models & Relationships

### VoucherCombination Model
```php
// app/Models/VoucherCombination.php
- belongsTo: voucherA (Voucher)
- belongsTo: voucherB (Voucher)
```

### Voucher Model (Enhanced)
```php
// Relations
- hasMany: allowedCombinations (VoucherCombination)
- hasMany: reverseCombinations (VoucherCombination)

// Helper Methods
- getCombinationType(): string  // 'shipping' atau 'discount'
- canCombineWith(Voucher $other): bool  // Validate combination
- isAllowedCombination(Voucher $other): bool  // Check DB rules
```

### Cart Model (Enhanced)
```php
// Relations
// (No new relations, uses array in JSON)

// Methods
- getAppliedVouchers(): Collection  // Get applied vouchers with data
- canAddMoreVouchers(): bool  // Check if can add (max 2)
- clearVoucher(): void  // Clear all vouchers
```

## Services

### VoucherService (Enhanced)

#### Public Methods
```php
addVoucher(Cart $cart, string $voucherCode): array
  // Tambah voucher ke cart dengan full validation
  // Returns: [success, message, voucher, discount_data]

removeVoucher(Cart $cart, string $voucherId): array
  // Hapus voucher dari cart

applyVouchersToOrder(array $voucherIds, array $orderData): array
  // Apply vouchers ke order dan tracking usage
  // Creates snapshots di voucher_usages table

getAvailableVouchers(int $limit = 10): Collection
  // Get list voucher untuk display di UI
```

#### Validation Chain
```
1. Find & validate voucher exists
2. Cek if already applied
3. Cek max 2 vouchers
4. If exist 1 voucher, validate combination
5. Validate quota
6. Validate user (members_only, usage_limit)
7. Validate minimum_purchase
8. Validate products/categories/brands
9. Validate shipping/payment methods
10. Validate flash_sale rule
11. Validate discount rule
12. Calculate discount amount
13. Update cart summary
```

## Controllers

### VoucherController (API Endpoints)

#### POST /cart/vouchers/add
```json
Request:
{
  "voucher_code": "PROMO10"
}

Response (Success):
{
  "success": true,
  "message": "Voucher 'Diskon 10%' berhasil diterapkan.",
  "data": {
    "vouchers": [...],
    "total_discount": 10000,
    "total_shipping_discount": 0,
    "cart_summary": {...}
  }
}

Response (Error - 422):
{
  "success": false,
  "message": "Kuota voucher sudah habis.",
  "errors": {"voucher": ["..."]}
}
```

#### POST /cart/vouchers/remove
```json
Request:
{
  "voucher_id": "uuid-of-voucher"
}
```

#### GET /cart/vouchers/available
```json
Response:
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "code": "PROMO10",
      "name": "Diskon 10%",
      "description": "...",
      "type": "percent",
      "type_label": "Diskon Persen",
      "value": 10,
      "maximum_discount": 50000,
      "label": "Hot Sale",
      "badge_color": "#FF6B6B",
      "is_sold_out": false,
      "remaining_quota": 100,
      "is_combinable": true,
      "minimum_purchase": 50000,
      "image_path": "..."
    }
  ]
}
```

#### GET /cart/vouchers/current
```json
Response:
{
  "success": true,
  "data": {
    "vouchers": [...],
    "can_add_more": true,
    "total_discount": 10000,
    "total_shipping_discount": 0
  }
}
```

#### POST /cart/vouchers/validate
```json
Request:
{
  "voucher_code": "PROMO10"
}

Response:
{
  "success": true,
  "data": {
    "id": "uuid",
    "code": "PROMO10",
    "name": "Diskon 10%",
    ...
  }
}
```

#### POST /cart/vouchers/clear
```json
Response:
{
  "success": true,
  "message": "Semua voucher berhasil dihapus."
}
```

## Views & Components

### Blade Component: voucher-section.blade.php
Located at: `resources/views/components/voucher-section.blade.php`

Props:
```blade
@props([
  'appliedVouchers' => null,  // Collection of applied vouchers
  'cartSummary' => null,      // Array with discount info
])
```

Features:
- Display applied vouchers dengan remove button
- Input form untuk manual kode entry
- Carousel dari available vouchers dengan copy button
- Real-time error/success messages
- Responsive design

Usage di Cart:
```blade
<x-voucher-section 
    :appliedVouchers="$cart->getAppliedVouchers()"
    :cartSummary="[
        'total_discount' => $cart->total_discount_amount ?? 0,
        'total_shipping_discount' => $cart->total_shipping_discount ?? 0,
    ]"
/>
```

## Implementation Steps

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Seed Sample Vouchers (Optional)
Create allowed combinations di admin panel atau seed:
```php
VoucherCombination::create([
  'voucher_a_id' => 'uuid-diskon-10',
  'voucher_b_id' => 'uuid-gratis-ongkir',
  'is_allowed' => true,
  'rule_description' => 'Diskon 10% dapat dikombinasi dengan Gratis Ongkir',
]);
```

### 3. Update Voucher Attributes
Di admin panel, set untuk setiap voucher:
- `is_combinable`: true/false
- `combination_type`: shipping/discount/both

### 4. Test Flow
1. Add product to cart
2. Go to cart page
3. See available vouchers carousel
4. Copy or manually input voucher code
5. Apply voucher (validation should run)
6. See discount breakdown
7. Try add 2nd voucher (should validate combination)
8. Go to checkout
9. Final grand total should reflect all discounts

## Error Handling

### Common Error Messages
```
"Voucher tidak valid atau tidak tersedia."
"Voucher sudah kadaluarsa."
"Kuota voucher sudah habis. Gunakan voucher lain."
"Anda tidak memenuhi syarat untuk voucher ini."
"Anda sudah mencapai batas penggunaan voucher ini."
"Minimum pembelian belum tercapai. Minimum: Rp50.000"
"Salah satu atau kedua voucher ini tidak dapat dikombinasikan dengan voucher lain."
"Tidak dapat menggabungkan dua voucher dengan tipe sama. Pilih satu voucher untuk diskon dan satu untuk gratis ongkir."
"Voucher '{new}' tidak dapat dikombinasikan dengan '{existing}'."
"Maksimal 2 voucher dapat digunakan. Silakan hapus voucher lain terlebih dahulu."
"Voucher ini sudah diaplikasikan pada keranjang."
```

## Security Considerations

1. **Quota Locking**: Gunakan `SELECT ... FOR UPDATE` saat increment usage untuk prevent double-use
2. **User Validation**: Verify user_id match dengan cart/order
3. **Rate Limiting**: Implement rate limit untuk API endpoints
4. **Snapshot**: VoucherUsage menyimpan snapshot untuk audit trail
5. **Transaction**: Apply vouchers to order dalam DB transaction

## Performance Optimizations

### Eager Loading
```php
// Di CartController saat load cart:
$cart = Cart::with(['items.product'])->find($id);

// Di checkout:
$cart = Cart::with([
    'items.product',
    'user',
])->find($id);
```

### Query Optimization
- Use `whereIn()` untuk batch operations
- Cache available vouchers (5 min TTL)
- Index pada `vouchers.code`, `vouchers.is_active`, `vouchers.start_at`

## Admin Panel Integration (TODO)

Perlu tambahan di admin:
1. Manage voucher combination rules
2. View voucher usage statistics
3. Manual quota adjustment
4. Audit trail viewing

## Payment Gateway Integration (TODO)

Update payment gateway call untuk include:
```php
$paymentData = [
    'subtotal' => $order->subtotal,
    'discount' => $order->total_discount_amount,
    'shipping_discount' => $order->total_shipping_discount,
    'shipping_cost' => $order->shipping_cost,
    'service_fee' => $order->service_fee,
    'grand_total' => $order->grand_total,
    'vouchers' => $order->vouchers,  // Array of applied vouchers
];
```

## Testing

### Unit Tests Needed
```php
- VoucherService::addVoucher() - valid case
- VoucherService::addVoucher() - quota exceed
- VoucherService::addVoucher() - combination invalid
- Voucher::canCombineWith() - valid combination
- Voucher::canCombineWith() - invalid combination
- Cart::getAppliedVouchers()
- Cart::canAddMoreVouchers()
```

### Integration Tests Needed
```php
- Apply 1 voucher -> check calculation
- Apply 2 vouchers -> check combination + calculation
- Remove voucher -> check recalculation
- Apply invalid combination -> error
- Checkout with vouchers -> order creation
```

## Future Enhancements

1. **Tiered Discounts**: Apply multiple times (bundle vouchers)
2. **Scheduled Campaigns**: Auto-apply vouchers based on rules
3. **User Segments**: Different vouchers untuk different user groups
4. **A/B Testing**: Test voucher effectiveness
5. **Analytics Dashboard**: Real-time voucher performance metrics

---

## Related Files

- Models: `app/Models/Voucher.php`, `app/Models/VoucherCombination.php`, `app/Models/Cart.php`, `app/Models/Order.php`
- Services: `app/Services/VoucherService.php`
- Controllers: `app/Http/Controllers/VoucherController.php`
- Views: `resources/views/components/voucher-section.blade.php`, `resources/views/cart/index.blade.php`
- Routes: `routes/web.php` (cart.vouchers.* group)
- Migrations: `database/migrations/2026_07_10_200000_update_vouchers_table_for_combinations.php`
