# Voucher API Testing Guide

## Base URL
```
http://localhost:8000/cart/vouchers
```

## Authentication
All endpoints require authenticated user (login first)

## Endpoints

### 1. Get Available Vouchers
```bash
curl -X GET "http://localhost:8000/cart/vouchers/available?limit=10" \
  -H "Accept: application/json" \
  -H "Cookie: XSRF-TOKEN=...; laravel_session=..."
```

**Response Example:**
```json
{
  "success": true,
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "code": "DISKON10",
      "name": "Diskon 10%",
      "description": "Dapatkan diskon 10% untuk pembelian minimal Rp50.000",
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
      "image_path": "/storage/vouchers/..."
    }
  ]
}
```

### 2. Validate Voucher
```bash
curl -X POST "http://localhost:8000/cart/vouchers/validate" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d '{"voucher_code": "DISKON10"}'
```

**Success Response:**
```json
{
  "success": true,
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "code": "DISKON10",
    "name": "Diskon 10%",
    "type": "percent",
    "type_label": "Diskon Persen",
    "value": 10,
    "label": "Hot Sale",
    "badge_color": "#FF6B6B",
    "is_combinable": true,
    "is_sold_out": false,
    "remaining_quota": 100
  }
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Kuota voucher sudah habis.",
  "errors": {
    "voucher": ["Kuota voucher sudah habis."]
  }
}
```

### 3. Add Voucher to Cart
```bash
curl -X POST "http://localhost:8000/cart/vouchers/add" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d '{"voucher_code": "DISKON10"}'
```

**Success Response:**
```json
{
  "success": true,
  "message": "Voucher 'Diskon 10%' berhasil diterapkan.",
  "data": {
    "vouchers": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "code": "DISKON10",
        "name": "Diskon 10%",
        "type": "percent",
        "discount_amount": 5000,
        "shipping_discount": 0,
        "value": 10
      }
    ],
    "total_discount": 5000,
    "total_shipping_discount": 0,
    "cart_summary": {
      "subtotal": 50000,
      "discount": 5000,
      "final_subtotal": 45000
    }
  }
}
```

**Error Responses:**

Combination Invalid (422):
```json
{
  "success": false,
  "message": "Tidak dapat menggabungkan dua voucher dengan tipe sama. Pilih satu voucher untuk diskon dan satu untuk gratis ongkir.",
  "errors": {
    "voucher": ["Tidak dapat menggabungkan dua voucher dengan tipe sama..."]
  }
}
```

Max Vouchers (422):
```json
{
  "success": false,
  "message": "Maksimal 2 voucher dapat digunakan. Silakan hapus voucher lain terlebih dahulu.",
  "errors": {
    "voucher": ["Maksimal 2 voucher dapat digunakan..."]
  }
}
```

Quota Exceeded (422):
```json
{
  "success": false,
  "message": "Kuota voucher sudah habis. Gunakan voucher lain.",
  "errors": {
    "voucher": ["Kuota voucher sudah habis. Gunakan voucher lain."]
  }
}
```

### 4. Get Current Applied Vouchers
```bash
curl -X GET "http://localhost:8000/cart/vouchers/current" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "vouchers": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "code": "DISKON10",
        "name": "Diskon 10%",
        "type": "percent",
        "discount_amount": 5000,
        "shipping_discount": 0,
        "value": 10
      }
    ],
    "can_add_more": true,
    "total_discount": 5000,
    "total_shipping_discount": 0
  }
}
```

### 5. Remove Voucher from Cart
```bash
curl -X POST "http://localhost:8000/cart/vouchers/remove" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d '{"voucher_id": "550e8400-e29b-41d4-a716-446655440000"}'
```

**Success Response:**
```json
{
  "success": true,
  "message": "Voucher berhasil dihapus.",
  "data": {
    "vouchers": [],
    "total_discount": 0,
    "total_shipping_discount": 0,
    "cart_summary": {
      "subtotal": 50000,
      "discount": 0,
      "final_subtotal": 50000
    }
  }
}
```

### 6. Clear All Vouchers
```bash
curl -X POST "http://localhost:8000/cart/vouchers/clear" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token"
```

**Response:**
```json
{
  "success": true,
  "message": "Semua voucher berhasil dihapus."
}
```

## JavaScript Testing Examples

### Load Available Vouchers
```javascript
fetch('/cart/vouchers/available?limit=10')
  .then(r => r.json())
  .then(data => console.log(data));
```

### Add Voucher
```javascript
fetch('/cart/vouchers/add', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
  },
  body: JSON.stringify({ voucher_code: 'DISKON10' })
})
.then(r => r.json())
.then(data => {
  if (data.success) {
    console.log('✓ Voucher applied!', data.data);
    location.reload(); // Reload untuk update UI
  } else {
    console.error('✗ Error:', data.message);
  }
});
```

### Remove Voucher
```javascript
fetch('/cart/vouchers/remove', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
  },
  body: JSON.stringify({ voucher_id: 'uuid-here' })
})
.then(r => r.json())
.then(data => {
  if (data.success) {
    location.reload();
  }
});
```

## Postman Testing

1. Create new collection "Bread Heaven - Voucher API"
2. Add variable: `base_url = http://localhost:8000`
3. Add variable: `csrf_token = <get from page>`

### For Each Request:
- URL: `{{base_url}}/cart/vouchers/add`
- Method: POST
- Headers:
  ```
  Content-Type: application/json
  X-CSRF-TOKEN: {{csrf_token}}
  ```
- Body (raw JSON):
  ```json
  {"voucher_code": "DISKON10"}
  ```
- Cookies: Copy from browser

## Manual Testing Checklist

- [ ] Login dengan user account
- [ ] Go to /cart page
- [ ] See voucher section with available vouchers carousel
- [ ] Copy a voucher code button works
- [ ] Enter voucher code manually and apply
- [ ] Voucher appears in applied section
- [ ] Discount calculated correctly
- [ ] Try add 2nd voucher (should work if combination valid)
- [ ] Try add invalid combination (should error)
- [ ] Try add 3rd voucher (should error max 2)
- [ ] Remove voucher works
- [ ] Clear all vouchers works
- [ ] Go to checkout with vouchers
- [ ] Vouchers shown in order summary
- [ ] Grand total calculated correctly
- [ ] Complete payment flow

## Performance Notes

- Voucher listing should cache di frontend (5 min)
- Use pagination jika vouchers > 50
- Lazy load carousel items saat scroll
- Debounce input field untuk avoid multiple requests
