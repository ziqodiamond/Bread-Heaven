# Cart Page Testing Guide

## Quick Test Steps

### 1. Setup
- Navigate to http://127.0.0.1:8000/products
- Add 2-3 items to cart

### 2. Cart Page Test
- Go to http://127.0.0.1:8000/cart
- Should see cart items with quantity controls

### 3. Quantity Update
- Click + or - buttons on items
- Verify:
  - No page reload
  - Quantity updates instantly
  - Subtotal updates
  - Order summary updates

### 4. Item Removal
- Click "Hapus" button on any item
- Verify:
  - Item disappears without reload
  - Total recalculates
  - Item count updates

### 5. Voucher Loading
- Scroll to "Pilih Voucher" section
- Verify:
  - Vouchers load without page reload
  - Cards display as tickets with:
    - Image on left (1:1 ratio)
    - Title in middle
    - Minimum purchase rules
    - "Selengkapnya" link
    - "Pakai" button on right

### 6. Voucher Application
- Click "Pakai" button on a voucher
- Verify:
  - Button shows loading state
  - Voucher moves to "Voucher Aktif" section
  - Discount amount updates in order summary
  - Available vouchers refresh

### 7. Voucher Details Modal
- Click "Selengkapnya" link on any voucher
- Verify:
  - Modal opens with voucher details
  - Shows image, type, minimum purchase, quota, end date
  - "Gunakan Voucher" button works

### 8. Applied Voucher Removal
- Click "Batal" on applied voucher
- Verify:
  - Voucher removed from applied list
  - Discount removed from summary
  - Voucher reappears in available list

### 9. Button States
- Try to apply voucher without meeting minimum purchase
- Verify:
  - Button is disabled (gray)
  - Hover shows no effect
- Once requirements met:
  - Button becomes enabled (blue)

### 10. Multiple Vouchers
- Apply two different vouchers
- Verify:
  - Both show in "Voucher Aktif" section
  - Can't apply third voucher (button disabled)
  - Both discounts applied to total

## Browser Console
- Open F12 Developer Tools → Console
- Should see NO JavaScript errors
- Successful fetch requests should be visible in Network tab

## Key Features Verified
- ✓ No page reload on any action
- ✓ Alpine.js reactive updates
- ✓ Async fetch API calls
- ✓ Dynamic price calculations
- ✓ Ticket-style voucher cards
- ✓ Modal functionality
- ✓ Button state management
- ✓ Error handling (check console)

## Troubleshooting

### Page not loading
- Check Laravel server is running: `php artisan serve`
- Check port 8000 is not blocked

### Fetch requests failing
- Check browser console for errors
- Verify CSRF token is in meta tag
- Check Accept header is set to application/json

### Vouchers not loading
- Check VoucherService::getAvailableVouchers() returns data
- Check cart has items (required for availability check)
- Check browser network tab for API response

### Styling issues
- Verify Tailwind CSS is loaded (check network tab)
- Clear browser cache (Ctrl+Shift+Delete)
- Restart Laravel dev server
