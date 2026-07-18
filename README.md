# Stilla — Scandinavian Furniture Storefront (WordPress)

A self-contained WordPress + Docker furniture storefront with a custom **Stilla** theme that
recreates and *improves* the editorial, design-led feel of https://blastation.com — sticky
nav, parallax hero, draggable product carousels, scroll-reveal, a startech-style catalogue,
individual product pages, a shopping cart, customer accounts, Stripe checkout, and built-in
SEO.

> Fictional brand "Stilla" with products (Måne Sofa, Frankfurt Chair, Terra Lounge, …) as
> stand-ins for Blå Station's real line. Swap copy/imagery for your own.

## Screenshots

| Home | Catalogue | Product page |
|------|-----------|--------------|
| ![Home](docs/home.png) | ![Catalogue](docs/catalog.png) | ![Product](docs/product.png) |

| Client reviews | Sign in | Create account |
|----------------|---------|----------------|
| ![Clients](docs/clients.png) | ![Login](docs/login.png) | ![Register](docs/register.png) |

## Features

- **Editorial homepage** — parallax hero, animated marquee, draggable product carousels
  (SOMA / FRANKFURTER families) with prev-next + dots, tilt-on-hover editorials, a showreel
  block, and a newsletter footer. Respects `prefers-reduced-motion`.
- **Startech-style catalogue** (`/products/`) — sticky sidebar with live search, category
  checkboxes (Sofas / Chairs / Stools / Tables / Outdoor), collection checkboxes
  (SOMA / FRANKFURTER / OUTDOOR), a dual price slider, an "In stock only" toggle, and a
  sort dropdown. All filtering is instant client-side.
- **Product detail pages** (`/product/<slug>/`) — minimal layout with sticky gallery +
  thumbnail switcher, price (incl. VAT), stock badge, quantity stepper, Add to cart / Buy now
  / Request a quote, and Specifications / Delivery & care accordions.
- **Shopping cart** — session-based, slide-in drawer with live count badge, plus a dedicated
  `/cart/` page with quantity steppers and remove.
- **Customer accounts** — separate, professionally designed **Sign in** (`/login/`) and
  **Create account** (`/register/`) pages with a split-screen brand panel + form, floating
  labels, and a "Forgot password?" reset flow. Logged-in users get a **My Account**
  dashboard with order history.
- **Stripe payments** — single-product "Buy now" and multi-item cart checkout both create
  Stripe Checkout Sessions and redirect to hosted checkout. Orders are recorded against the
  user on return. *(Test card: 4242 4242 4242 4242.)*
- **Quote requests** — every product has a "Request a quote" modal saved as a private
  inquiry post in WP admin.
- **Client stories & reviews** — a "Trusted by Studios & Brands" section with client logos
  and four customer reviews (with star ratings) framed as delivered projects.
- **SEO built in** — per-page `<title>` + meta description, canonical URL, Open Graph +
  Twitter cards, JSON-LD structured data (Organization, Product + Offer, BreadcrumbList),
  an XML `sitemap.xml`, and a `robots.txt` that points crawlers at the sitemap.
- **Real photography** — product/editorial imagery pulled from Unsplash via `stilla_img()`.

## Stack

- WordPress 6.x (PHP 8.3-FPM) in Docker
- MariaDB 11.4
- nginx on port **8080**
- Custom theme: `themes/stilla`

## Run

```bash
cd /home/rownak/projects/blastation-style
docker compose up -d
# open http://localhost:8080
```

## Stop

```bash
docker compose down        # keep the database
docker compose down -v     # wipe the database
```

## Admin

- URL:  http://localhost:8080/wp-admin
- User: `admin`
- Pass: `Admin@12345!`  *(change it!)*
- Change: WP admin → Users, or
  ```bash
  docker compose exec -T wordpress wp user update admin --user_pass='NEW' --allow-root
  ```

## Configure Stripe (required for live payments)

Open `themes/stilla/functions.php` and set your keys near the top:

```php
define( 'STILLA_STRIPE_PK', 'pk_test_xxx' );
define( 'STILLA_STRIPE_SK', 'sk_test_xxx' );
```

Without real keys, checkout redirects to Stripe but won't process — it fails gracefully with
a clear message. After editing, flush rewrites:

```bash
docker compose exec -T wordpress wp rewrite flush --hard --allow-root
```

## Deploy to Railway

The project ships a self-contained `Dockerfile` (bakes the custom theme + mu-plugin into the
official WordPress image and runs nginx + php-fpm via supervisord). Railway builds it and
provides MySQL.

1. Create a **Railway** project and add the **MySQL** plugin (creates `MYSQL_HOST`,
   `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_DATABASE` env vars).
2. Add a service from this GitHub repo (New → Deployment Service → GitHub →
   `MohammadMuntasirKabir/stilla-furniture`). Railway auto-detects `railway.json` (Docker
   builder, exposes port 80).
3. In the service **Variables**, add:
   - `WORDPRESS_DB_HOST` = `${{MySQL.MYSQL_HOST}}` (use the MySQL plugin reference)
   - `WORDPRESS_DB_NAME` = `${{MySQL.MYSQL_DATABASE}}`
   - `WORDPRESS_DB_USER` = `${{MySQL.MYSQL_USER}}`
   - `WORDPRESS_DB_PASSWORD` = `${{MySQL.MYSQL_PASSWORD}}`
   - `WP_URL` = your Railway domain (e.g. `https://stilla.up.railway.app`)
   - `ADMIN_PASS` (optional) to override the default admin password
4. Deploy. On first boot `docker/init.sh` runs `wp core install`, activates the **stilla**
   theme, and flushes rewrites. The `dynamic-url` mu-plugin makes WordPress follow the
   Railway URL automatically.
5. Optional: add a **Custom Domain** + Railway's auto-TLS. Change `WORDPRESS_DB_PASSWORD` and
   the admin password before going live, and set real Stripe keys in `functions.php`
   (`STILLA_STRIPE_PK` / `STILLA_STRIPE_SK`).

> Free/teardown note: Railway's MySQL volume persists between deploys. If you wipe the DB, the
> WP install + theme re-run automatically on next boot. Product/catalogue data is code-defined
> in `stilla_products()` (functions.php), so it survives a fresh install.



The site runs locally on port 8080. To share it:

```bash
cloudflared tunnel --url http://localhost:8080
```

It prints a `https://*.trycloudflare.com` URL. A mu-plugin
(`wp-content/mu-plugins/dynamic-url.php`) makes WordPress URLs follow the incoming Host, so
assets and the cart work behind any tunnel without hardcoding URLs. *Quick-tunnel URLs rotate
on restart — for a stable URL use a named Cloudflare tunnel or a domain.*

## SEO endpoints

- `https://<site>/sitemap.xml` — XML sitemap (home, catalogue, every product).
- `https://<site>/robots.txt` — allows indexing, disallows auth/cart/account, links the sitemap.

## Project structure

```
themes/stilla/
├── front-page.php          # hero, marquee, SOMA/FRANKFURTER carousels, showreel,
│                            #   editorials, "Trusted by Studios & Brands" reviews
├── header.php / footer.php  # sticky nav + search drawer + cart drawer / newsletter footer
├── archive-products.php     # /products — startech-style grid + sidebar filters
├── single-product.php       # /product/<slug>/ — gallery, specs, related, quote modal
├── login.php / register.php # split-screen auth pages
├── account.php              # logged-in dashboard (order history)
├── cart.php                 # /cart/ — line items + checkout
├── functions.php            # image helper, product data, routing, auth, cart,
│                            #   Stripe, quote AJAX, SEO (meta/OG/sitemap/robots)
└── assets/
    ├── css/main.css         # all styling
    └── js/main.js           # carousel, parallax, reveal, nav, tilt, filters,
                             #   cart, quote modal, star ratings, forgot-password
docs/                        # README screenshots
```

## Editing notes

- Theme files are bind-mounted: edit on disk → refresh, no rebuild needed.
- Products are the single source of truth in `stilla_products()` (functions.php):
  `slug, code, name, family, type, image key, price, stock, blurb, specs[], gallery[]`.
- After changing product data or routes, flush rewrites
  (`docker compose exec -T wordpress wp rewrite flush --hard --allow-root`).
- Imagery is curated Unsplash IDs in `stilla_images()`; swap an ID or use
  `wp_get_attachment_image_url()` for your own uploads.

## Fictional brand note

"Stilla" and its products (Måne, Frankfurt, Terra, …) are fictional placeholders for Blå
Station's real catalogue. Replace all copy and imagery with your own before going live.
