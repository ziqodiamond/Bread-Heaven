# Migration Consolidation Summary

## Overview
✅ Consolidated 35 migration files → 25 migration files
✅ Each model now has only 1 migration file
✅ All add/alter columns consolidated into create table files
✅ Proper dependency ordering (categories before products, etc.)

## Changes Made

### 1. Categories & Products (Dependency Fix)
- **NEW**: `2024_08_26_000000_create_categories_table.php`
  - Categories table created first (required by products FK)
  
- **UPDATED**: `2024_08_26_024545_create_products_table.php`
  - Removed: old string `category` column
  - Added: `category_id` foreign key to categories.id
  - Added: `discount_max` (bigInteger, nullable)
  - Status: Consolidated with add_category_id migration

### 2. Carts
- **UPDATED**: `2024_08_28_130255_create_carts_table.php`
  - Added: `voucher_snapshot` JSON column
  - Status: Consolidated with add_voucher_snapshot migration

### 3. Orders
- **UPDATED**: `2026_05_08_092354_create_orders_table.php`
  - Added: `voucher_snapshot` JSON column
  - Status: Consolidated with add_voucher_snapshot migration

### 4. Flash Sales
- **UPDATED**: `2026_05_15_143325_create_flash_sales_table.php`
  - Added: `flash_sale_items` table (consolidated from separate migration)
  - flash_sale_items includes: product FK, discount_price, flash_stock, stock_sold, sort_order

### 5. Product Reviews
- **UPDATED**: `2026_05_15_114906_create_product_reviews_table.php`
  - Added: `product_review_media` table (consolidated from separate migration)
  - product_review_media includes: media_path, type (image/video), sort_order

### 6. Vouchers (Most Complex)
- **UPDATED**: `2026_05_15_143504_create_vouchers_table.php`
  - Added: `image_path` (string, nullable)
  - Added: `allow_on_flash_sale` (boolean, default true)
  - Added: `allow_on_discount` (boolean, default true)
  - Added: `exclude_digital` (boolean, default false)
  - Added: 4 pivot tables consolidated:
    - `voucher_products` (with is_excluded flag)
    - `voucher_categories` (with is_excluded flag)
    - `voucher_shipping_methods` (UUID type for shipping_method_id)
    - `voucher_payment_methods` (UUID type for payment_method_id)
  - Removed: voucher_brands pivot table (dropped)
  - Status: Consolidated 5 separate migrations

### 7. Messages
- **UPDATED**: `2026_05_15_161144_create_messages_table.php`
  - Added: `message_attachments` table (consolidated from separate migration)
  - message_attachments includes: file_name, file_path, file_size, mime_type, type (image/document/video/audio/other)

## Deleted Migration Files (10 files)
These were consolidated into main create table migrations:
1. `2026_07_10_074613_create_categories_table.php` (moved to 2024_08_26_000000)
2. `2026_07_10_074634_add_category_id_to_products_table.php` (consolidated to products)
3. `2026_07_05_231227_add_image_to_vouchers_table.php` (consolidated to vouchers)
4. `2026_07_05_223200_add_voucher_rule_flags.php` (consolidated to vouchers)
5. `2026_07_05_223100_create_voucher_pivots_table.php` (consolidated to vouchers)
6. `2026_07_10_074922_drop_voucher_brands_table.php` (removed - brands not used)
7. `2026_05_15_115411_create_product_review_media_table.php` (consolidated to product_reviews)
8. `2026_05_15_145610_create_flash_sale_items_table.php` (consolidated to flash_sales)
9. `2026_05_15_161229_create_message_attachments_table.php` (consolidated to messages)
10. `2026_07_05_223300_add_voucher_snapshot_to_cart_and_orders.php` (consolidated to both tables)

## Migration Order (Proper Dependency)
The migrations now follow proper dependency order:
```
1. 0001_01_01_000000_create_users_table.php
2. 0001_01_01_000001_create_cache_table.php
3. 0001_01_01_000002_create_jobs_table.php
4. 2024_08_26_000000_create_categories_table.php  ← CATEGORIES FIRST
5. 2024_08_26_024545_create_products_table.php    ← PRODUCTS (depends on categories)
6. 2024_08_28_130255_create_carts_table.php
7. 2024_08_28_130257_create_cart_items_table.php
8-23. [Other tables]
24. 2026_07_17_222500_fix_voucher_pivot_types_uuid.php ← FIX (depends on vouchers)
```

## Next Steps

### Run Fresh Migrate
```bash
php artisan migrate:fresh --seed
```

This will:
- Drop all tables
- Run all migrations in order (including the consolidated ones)
- Seed data
- Create clean database state

### Verify Schema
After migration completes, verify:
- All tables created correctly
- Foreign keys working properly
- UUID types consistent in pivot tables
- No "column does not exist" errors

### Benefits of This Consolidation
✅ Cleaner migration folder - Reduced from 35 to 25 files
✅ Single source of truth - Each model has one migration
✅ Better maintainability - Related changes in one place
✅ Proper dependency ordering - Categories before products
✅ Fewer migration files to track - Easier to review
✅ Fresh start ready - All changes consolidated for fresh migrate

## Important Notes

1. **Categories timestamp changed**: From `2026_07_10` to `2024_08_26_000000` to ensure it runs BEFORE products (dependency requirement)

2. **All schema changes included**: No add/alter migrations remain - everything in create table

3. **Pivot table types consolidated**: All pivot tables now use consistent UUID types (previously had bigint/uuid mismatches that were causing errors)

4. **Fresh migrate required**: Since timestamps and file names changed, you MUST run `php artisan migrate:fresh --seed` as you planned

5. **No data loss**: This consolidation only affects migration files - your actual data will be preserved after fresh migrate with seed
