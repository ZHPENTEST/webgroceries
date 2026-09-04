# WebGroceries

Modern grocery e-commerce (PHP 8.2+, MySQL 8, PDO, vanilla JS). Design follows TasteSkill-v2 (Emerald Signal `#0E9F6E`, Outfit type, asymmetric hero, bento grids, no Inter/purple/centered-hero).

## Setup
1. `cp .env.example .env` and set DB creds.
2. `mysql -u root < database/schema.sql && mysql -u root webgroceries < database/seed.sql`
3. `mkdir -p storage/logs public/assets/images/products && touch public/assets/images/products/.gitkeep`
4. `php -S localhost:8000 -t public` (or point Apache/Nginx docroot to `public/`).
5. Login: `admin@webgroceries.test / password` (admin), `aina@test.test / password` (customer).

## Test flows
- Customer: register → login → /shop search/filter → /product/{slug} → add to cart (fetch, no reload) → /cart qty/coupon FRESH10 → /checkout (address/delivery/payment) → /orders/{id} timeline.
- Admin: /admin → products CRUD + image upload → categories (protected delete) → orders status → coupons/customers.
- New: product reviews (verified buyers), order cancel (stock restored) + reorder, flash-sale countdown, promo slider, delivery slot picker, saved-address picker, recently viewed, print invoice, admin sales chart, inline stock stepper, brute-force throttle.
- Security: PDO everywhere, `htmlspecialchars` via `e()` (+ JSON_HEX_* for embedded JSON), CSRF on POST + fetch header, session regenerate on login, admin gate on `/admin/*`, IDOR checks on orders/addresses, server-side stock + price recalc inside transaction, safe re-encoded uploads with random names.
- Hardening: strict/HttpOnly/SameSite-Lax cookies, 60-min idle timeout, headers (nosniff, DENY framing, CSP, referrer/permissions policies), login throttle (5 tries/IP/15 min → 429), POST-only logout, open-redirect guard, least-privilege DB user, PHP execution off in upload dirs.

## Structure
`public/index.php` router · `app/{Core,Models,Controllers,Views}` · `config/` · `routes` inline · `database/{schema,seed}.sql`.
Payment: `cod` + `mock_online` only — replace `Checkout::place` + `payments` layer for a real provider.

## Deploy
See `DEPLOY.md` (shared hosting, VPS, **free** options: Cloudflare Tunnel demo + InfinityFree permanent) and `deploy/` configs.
