# KurDom — Progress Tracker

**Platform kurir kota Dompu**
Tech Stack: Laravel 13 · Filament 5 · Livewire 4 · Docker · MySQL · Redis

---

## Phase 0 — Project Setup ✅

| # | Task | Status |
|---|------|--------|
| 0.1 | Docker Compose (app, nginx, mysql, redis, phpmyadmin) | ✅ |
| 0.2 | Laravel install | ✅ |
| 0.3 | Filament install | ✅ |
| 0.4 | SSL self-signed (kurdom.test) | ✅ |
| 0.5 | Git init + GitHub remote | ✅ |
| 0.6 | .env configuration | ✅ |
| 0.7 | Timezone Asia/Makassar | ✅ |
| 0.8 | 3 Filament panels (admin/seller/courier) | ✅ |
| 0.9 | ERD design | ✅ |
| 0.10 | Database schema documentation | ✅ |
| 0.11 | Wireframe (skipped) | ❌ |
| 0.12 | Phase 0 commit | ✅ |

**Progress: 11/12**

---

## Phase 1.1 — Database & Models ✅

| # | Task | Status |
|---|------|--------|
| 1 | users migration | ✅ |
| 2 | courier_profiles migration | ✅ |
| 3 | seller_profiles migration | ✅ |
| 4 | products migration | ✅ |
| 5 | orders migration | ✅ |
| 6 | order_items migration | ✅ |
| 7 | delivery_fee_configs migration | ✅ |
| 8 | ratings migration | ✅ |
| 9 | notifications migration | ✅ |
| 10 | User model + relationships | ✅ |
| 11 | CourierProfile model | ✅ |
| 12 | SellerProfile model | ✅ |
| 13 | Product model | ✅ |
| 14 | Order model | ✅ |
| 15 | OrderItem model | ✅ |
| 16 | DeliveryFeeConfig model | ✅ |
| 17 | Rating model | ✅ |
| 18 | Notification model | ✅ |
| 19 | Enums (UserRole, OrderStatus, OrderSource, VehicleType, etc.) | ✅ |
| 20 | AdminUserSeeder | ✅ |
| 21 | DeliveryFeeConfigSeeder | ✅ |
| 22 | migrate:fresh --seed verified | ✅ |

**Progress: 22/22**

---

## Phase 1.2 — Authentication & Access Control ✅

| # | Task | Status |
|---|------|--------|
| 1 | Phone-based Login (custom Login.php) | ✅ |
| 2 | Phone-based Register (custom Register.php) | ✅ |
| 3 | FilamentUser interface on User model | ✅ |
| 4 | canAccessPanel() role-based guards | ✅ |
| 5 | Panel branding (Admin=Amber, Seller=Emerald, Courier=Sky) | ✅ |
| 6 | Seller & Courier registration enabled, Admin disabled | ✅ |
| 7 | All 3 login pages return HTTP 200 | ✅ |
| 8 | Registration pages return HTTP 200 | ✅ |

**Progress: 8/8**

---

## Phase 1.3 — Service Layer ✅

| # | Task | Status |
|---|------|--------|
| 1 | PhoneLookupService | ✅ |
| 2 | DeliveryFeeService (Haversine + night surcharge) | ✅ |
| 3 | OrderService (create, claim, status transitions, cancel) | ✅ |
| 4 | State machine validation (invalid transitions rejected) | ✅ |
| 5 | All services tested via tinker | ✅ |

**Progress: 5/5**

---

## Phase 1.4 — Admin Panel ✅

| # | Task | Status |
|---|------|--------|
| 1 | UserResource (CRUD, role filter, toggle active) | ✅ |
| 2 | DeliveryFeeConfigResource (rate management) | ✅ |
| 3 | OrderResource (list, view, status/source filters) | ✅ |
| 4 | AdminStatsWidget (sellers, couriers, orders, completed) | ✅ |

**Progress: 4/4**

---

## Phase 1.5 — Seller Panel ✅

| # | Task | Status |
|---|------|--------|
| 1 | StoreProfile page (name, hours, open/close toggle) | ✅ |
| 2 | CreateOrder page (buyer lookup, coords, fee calc) | ✅ |
| 3 | OrderResource (seller's orders, cancel action) | ✅ |
| 4 | SellerStatsWidget (pending, transit, completed, revenue) | ✅ |

**Progress: 4/4**

---

## Phase 1.6 — Courier Panel ✅

| # | Task | Status |
|---|------|--------|
| 1 | ToggleAvailability page (online/offline) | ✅ |
| 2 | AvailableOrderResource (new orders, claim, 15s poll) | ✅ |
| 3 | MyOrderResource (status progression, cancel w/ reason) | ✅ |
| 4 | CourierStatsWidget (status, active, completed, earnings) | ✅ |

**Progress: 4/4**

---

## Test Credentials

| Role | Phone | Password | URL |
|------|-------|----------|-----|
| Admin | 08000000001 | password | /admin |
| Seller | 08000000002 | password | /seller |
| Courier | 08000000003 | password | /courier |

---

## Verified ✅

- `migrate:fresh --seed` — 11 migrations, 2 seeders
- Panel discovery — Admin: 3 resources, Seller: 1 resource + 3 pages, Courier: 2 resources + 2 pages
- `canAccessPanel()` — Role guards working correctly
- DeliveryFeeService — Haversine distance + fee calculation
- PhoneLookupService — User lookup by phone
- OrderService — Full lifecycle: New → Courier Assigned → Picked Up → In Delivery → Completed
- OrderService — Cancel flow + invalid transition rejection
- HTTP 200 — All login pages (/admin/login, /seller/login, /courier/login)
- HTTP 200 — Registration pages (/seller/register, /courier/register)
