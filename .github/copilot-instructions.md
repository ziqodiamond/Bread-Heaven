# Bread Heaven AI Coding Instructions

Repository: Bread Heaven

Laravel 12 E-Commerce with Midtrans Payment Gateway + Biteship Shipping API.

This project is intended to be production-ready. Every change must preserve business rules, data consistency, and existing architecture.

---

# General Principles

Always study the existing code before making changes.

Never generate isolated code that ignores the existing architecture.

Always search for:

- related models
- controllers
- services
- form requests
- jobs
- listeners
- observers
- migrations
- blade views
- javascript
- routes
- helpers

before implementing a feature.

If existing logic already exists, extend it instead of creating duplicate implementations.

Never rewrite an entire feature unless explicitly requested.

---

# Tech Stack

Laravel 12

PHP 8.2+

Blade

Tailwind CSS

Alpine.js

Flowbite

Vite

MySQL

UUID Primary Keys

---

# Third Party Integrations

## Midtrans

Payment gateway:

- Snap
- Snap Token
- Webhook Notification
- Transaction Status
- Settlement
- Capture
- Pending
- Cancel
- Deny
- Expire
- Refund

Payment source of truth MUST come from Midtrans webhook.

Never trust frontend payment status.

Refund, cancel, expire and settlement must synchronize Order and PaymentTransaction.

---

## Biteship

Shipping provider:

- Shipping Rate
- Courier
- Tracking
- Shipment
- Waybill
- Shipping Cost

Shipping cost must always come from Biteship.

Never hardcode shipping prices.

Shipment status should synchronize Order status.

---

# Architecture

Use existing architecture.

Business logic belongs inside:

- Service Classes
- Action Classes
- Models (small helper methods only)

Controllers should remain thin.

Never place heavy business logic inside Blade.

Never place heavy business logic inside Controllers.

---

# Database Rules

## IMPORTANT

When modifying existing tables:

DO NOT create a new migration.

Instead:

- edit the ORIGINAL migration that created the table.

This project is still under active development.

Migration history should remain clean.

Create a new migration ONLY IF:

- creating an entirely new table
- adding a brand new feature that requires a new table

Do NOT create migrations such as:

add_xxx_to_orders_table

update_products_table

change_users_table

Instead modify the master migration directly.

---

# UUID

All models use UUID.

Never introduce auto increment IDs.

Always follow existing UUID implementation.

---

# Existing Business Features

Always preserve compatibility with:

- Cart
- Checkout
- Voucher
- Flash Sale
- Product Discount
- Shipping
- Payment
- Review
- Conversation
- Order
- Shipment
- Webhook

Never break these modules.

---

# Discount Rules

Discount priority:

Flash Sale

↓

Product Discount

↓

Voucher

Voucher must never overwrite Flash Sale logic unless explicitly designed.

Always respect voucher rules.

---

# Voucher Rules

Voucher may have restrictions based on:

- Product
- Category
- Payment Method
- Shipping Method
- Minimum Purchase
- Maximum Discount
- Start Date
- End Date
- Quota
- User Limit
- Members Only
- Stackable
- Free Shipping

Cart and Checkout must always validate voucher availability using the same logic.

Backend is the source of truth.

Frontend validation is only for UX.

---

# Shipping Rules

Shipping calculation must consider:

Destination

Origin

Weight

Courier

Service

Biteship API

Never calculate shipping manually.

---

# Payment Rules

Order amount must always equal:

Products

-

Discount

-

Shipping

-

Payment Fee

=

Grand Total

Never manually modify grand total without recalculating every component.

---

# Stock Rules

Stock reduction only occurs after successful payment.

Cancelled, expired or refunded orders must restore stock when applicable.

Never reduce stock before payment success.

---

# Webhook Rules

Webhook processing must:

be idempotent

avoid duplicate processing

log payload

validate signature

handle retries safely

Never process the same webhook twice.

---

# Code Style

Prefer:

Services

Repositories (if already used)

Form Requests

Policies

Observers

Events

Listeners

Enums (if project uses them)

Avoid duplicated logic.

Reuse existing helper methods.

---

# Frontend

Use:

Blade

Tailwind

Alpine

Flowbite

Reuse existing UI components.

Do not introduce new UI frameworks.

---

# Validation

Always validate:

authorization

ownership

stock

voucher

shipping

payment

Never trust frontend values.

---

# Performance

Avoid N+1 queries.

Use eager loading.

Paginate large datasets.

Cache expensive lookups when appropriate.

---

# Formatting

Follow existing coding style.

Respect existing naming.

Do not rename files unnecessarily.

Do not change formatting of unrelated code.

Keep comments in Indonesian if the surrounding file already uses Indonesian comments.

---

# Before Finishing Any Task

Verify:

✓ Existing features still work

✓ No duplicated business logic

✓ Relationships still valid

✓ Voucher still works

✓ Flash Sale still works

✓ Shipping still works

✓ Midtrans still works

✓ Biteship still works

✓ Payment calculation still correct

✓ Cart and Checkout remain synchronized

If modifying checkout, payment, shipping, voucher, or order logic, review all related files first before making changes.
