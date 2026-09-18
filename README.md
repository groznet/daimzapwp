# DaimZap — WordPress/WooCommerce theme

Custom classic theme for **DaimZap** (daimzap.ru), an auto parts catalog in Grozny.
The frontend is built specifically for parts discovery — SKU-first search, vehicle
make/model compatibility filtering, retail and wholesale paths. WooCommerce runs
the commerce engine underneath without showing through in the UI.

The theme ships **two complete visual directions** over identical markup — see
[Skins](#skins).

- **Requires:** WordPress 6.4+, WooCommerce 8.0+, PHP 7.4+
- **Text domain:** `daimzap` (interface language: Russian)
- **Template `.pot`:** `languages/daimzap.pot`

## Installation

Clone or copy this repository into `wp-content/themes/daimzap`, then activate it.
The compiled stylesheet is committed, so **no build step is required to run the
theme**.

```bash
cd wp-content/themes
git clone https://github.com/groznet/daimzapwp.git daimzap
```

On activation the theme creates the two vehicle compatibility attributes if the
store does not already have them (see below) and flushes rewrite rules once.

## Setup checklist

1. **Permalinks** — Settings → Permalinks, choose a pretty structure and save
   once so the vehicle archives resolve.
2. **Front page** — Settings → Reading, set a static front page and a separate
   posts page (`/blog`). The theme's `front-page.php` renders the custom home
   page; content written on the assigned page appears below the standard
   sections.
3. **Menus** — Appearance → Menus. Four locations are registered:
   `Основное меню` (header), `Меню каталога` (footer catalog column),
   `Меню в подвале`, `Служебные ссылки` (privacy, terms).
   With no catalog menu assigned, the footer falls back to the busiest product
   categories automatically.
4. **Contacts and home copy** — Appearance → Customize → *DaimZap: контакты* and
   *DaimZap: главная страница*. Phone, WhatsApp, email, address, hours, hero
   text and the wholesale page are all set here, never in templates.
5. **Wholesale page** — create a page, assign the *Оптовикам* page template, and
   select it in the Customizer so every "Стать оптовиком" button points at it.
6. **Contacts page** — optional; the *Контакты* page template renders the
   Customizer contact details as cards above the page content.
7. **Widgets** — the *Сайдбар каталога* area sits below the built-in catalog
   filters, for WooCommerce filter widgets. *Сайдбар блога* and *Подвал* are
   ordinary widget areas.

## Skins

Two finished looks, switchable in **Appearance → Customize → DaimZap: оформление**.
Templates, markup and behaviour are identical; only the compiled stylesheet and a
body class change. That is the whole point of the arrangement — a second look
costs a token file, not a second theme.

| Skin | Slug | Character |
|------|------|-----------|
| **Графит** (default) | `graphite` | Light and premium. Cool graphite neutrals, red signal accent, soft 6px corners, generous whitespace, sentence-case labels. Reads as a considered retail catalog. |
| **Терминал** | `terminal` | Dark and technical. Near-black panels, amber signal accent, square corners, hairline rules, monospaced SKUs / prices / counts, uppercase tracked labels, a faint measurement grid behind the hero. Reads as a workshop parts console. |

Product photography keeps a light plate in both skins (`--color-surface-media`),
because parts are shot on white.

### Reviewing them side by side

Logged-in users who can edit theme options get a **«Оформление»** menu in the
admin bar that switches skins on the current page via a `?dz-skin=` parameter.
The parameter is capability-gated, so an anonymous visitor cannot force a skin
into a shared page cache.

### How a skin is built

Skins are token sets, not stylesheet forks. Every component file under
`assets/css/src/parts/` is written against semantic tokens:

```
--color-surface-page / -card / -sunken / -inverse / -media
--color-text-strong / -base / -muted / -faint / -on-inverse / -on-accent
--color-border-subtle / -base / -strong / -emphasis / -inverse
--color-accent / -accent-hover / -accent-soft
--radius-card / --radius-control / --shadow-card / --shadow-raised
--font-sans / --font-data / --dz-price-family
--dz-label-transform / --dz-label-tracking / --dz-heading-weight / --dz-body-size
```

A skin entry (`assets/css/src/skins/<slug>.css`) imports the shared parts and
supplies values for those tokens. Where a skin needs to differ structurally — the
terminal skin's command-prompt search field, its status-dot stock badges, its
`//` section markers — it adds one extra layer (`parts/skin-terminal.css`) loaded
last, which wins on source order without specificity tricks.

To add a third skin: copy a skin entry, change the token values, register it via
the `daimzap_skins` filter, and add a build script.

**Note on `theme.json`:** its `styles` reference the same tokens
(`var(--color-surface-page, #ffffff)`) rather than literal colours, so the block
editor's global styles follow the active skin instead of fighting it.

## Vehicle compatibility

Car make and model are **WooCommerce global product attributes**, not custom
taxonomies:

| Role  | Attribute slug | Taxonomy        | Archive URL              |
|-------|----------------|-----------------|--------------------------|
| Make  | `car-make`     | `pa_car-make`   | `/catalog/marka/{term}/` |
| Model | `car-model`    | `pa_car-model`  | `/catalog/model/{term}/` |

This is what makes the Excel/1С import work without custom code: the WooCommerce
product importer maps attribute columns natively, and
`WC_Query::get_layered_nav_chosen_attributes()` already turns
`?filter_car-make=toyota` into a tax query on the product loop. The catalog
filters are core WooCommerce behaviour, not a parallel filtering system.

A product may carry several makes and several models; every template renders
compatibility conditionally and shows nothing when the data is absent.

To point the theme at attributes that already exist under different slugs, use
the `daimzap_vehicle_attributes` filter.

### Importing products

Product data maps onto standard WooCommerce fields:

| Excel column    | WooCommerce field                             |
|-----------------|-----------------------------------------------|
| `SKU`           | SKU                                           |
| `Name`          | Product name                                  |
| `Stock`         | Stock quantity (enable stock management)      |
| `Sale Price`    | Sale price                                    |
| `Regular Price` | Regular price                                 |
| `Categories`    | Product categories                            |
| `Car Makes`     | Attribute `car-make` (comma separated values) |
| `Car Models`    | Attribute `car-model` (comma separated)       |

Match on SKU when updating. Make both attributes **visible** and **used for
variations: no**; the theme reads them through the product attribute API.

## Catalog filtering

All filter state lives in the URL, so results are shareable and cacheable:

| Filter      | Query argument                        | Handled by                        |
|-------------|---------------------------------------|-----------------------------------|
| Make        | `filter_car-make=toyota,ford`         | WooCommerce layered nav           |
| Model       | `filter_car-model=corolla`            | WooCommerce layered nav           |
| Price       | `min_price` / `max_price`             | WooCommerce price filter          |
| In stock    | `in_stock=1`                          | `product_visibility` / `outofstock`|
| Sorting     | `orderby`                             | WooCommerce catalog ordering       |

Product search (`?s=…&post_type=product`) is given the same tax, meta, ordering
and price behaviour as the shop page, so the sidebar works identically on search
results.

## SKU search

- An **exact** SKU match redirects straight to the product page.
- A **partial** SKU match is merged into the normal search results alongside
  name and description matches. The lookup uses `wc_get_products( [ 'sku' => … ] )`
  — no custom SQL against post meta.

## Development

```bash
npm install
npm run build          # both skins, minified
npm run build:graphite # one skin
npm run dev            # watch graphite
npm run dev:terminal   # watch terminal
```

Tailwind CSS v4 with a CSS-first config. Each skin under
`assets/css/src/skins/` is an entry point that imports the shared component
layers from `assets/css/src/parts/` and supplies the token values. Tailwind
utilities are used for layout in markup; anything that repeats becomes a `dz-`
component class.

**Commit the built `assets/css/skin-*.css`** — the theme is deployed by file copy
and must not require a build on the server.

### Structure

```
functions.php          bootstrap only; every feature lives in inc/
inc/
  setup.php            theme supports, menus, image sizes, widget areas
  skins.php            skin registry, resolution, Customizer picker, admin bar
  assets.php           conditional enqueueing, WooCommerce asset trimming
  template-tags.php    icons, breadcrumbs, pagination, catalog context helpers
  template-functions.php  small core filters
  customizer.php       contacts and home page copy
  wholesale.php        wholesale application form handler
  class-daimzap-nav-walker.php
  woocommerce/
    setup.php          theme support, image sizes, placeholder, tabs, stock text
    attributes.php     vehicle attributes, archives, compatibility helpers
    hooks.php          loop and single-product hook re-wiring
    catalog.php        filter state, query modifications, filter URLs
    search.php         SKU-aware product search
    cart.php           cart fragments, SKU in line items
    account.php        My Account menu and orders query
template-parts/        header/, home/, catalog/, content/
page-templates/        wholesale, contacts
woocommerce/           template overrides (only where the design requires one)
assets/
  css/src/parts/       shared component layers, written against semantic tokens
  css/src/skins/       one entry per skin (token values + any extra layer)
  css/skin-*.css       compiled output, committed
  js/, images/
```

### JavaScript

Three small vanilla modules, all deferred and loaded only where needed:

| File         | Loaded on            | Does                                      |
|--------------|----------------------|-------------------------------------------|
| `site.js`    | every page           | mobile drawer, submenu toggles            |
| `catalog.js` | catalog listings     | filter drawer, sort auto-submit, list search |
| `product.js` | single product       | gallery, quantity stepper, tabs           |

Everything works without JavaScript: filters are links, sorting has a submit
button, gallery thumbnails are links to full-size images, and tab panels are all
visible.

### Performance notes

- The theme does **not** declare `wc-product-gallery-zoom/lightbox/slider`
  support, so flexslider, photoswipe and jquery-zoom never load. The gallery is
  a ~40-line custom one.
- `woocommerce-layout` and `woocommerce-smallscreen` stylesheets are dequeued;
  `woocommerce-general` is kept because it styles components the theme does not
  own.
- `wc-cart-fragments` is limited to pages where a cart action can happen
  (filter: `daimzap_limit_cart_fragments`).
- `wc-blocks-style` is dropped on pages that render no blocks.
- Font Awesome is loaded from a CDN and can be disabled or self-hosted via the
  `daimzap_load_font_awesome` filter (re-register under the `daimzap-icons`
  handle to serve a local copy).

## Wholesale applications

The theme ships the **front end** of the wholesale flow only — the approval
workflow is intentionally not invented. A valid submission:

1. is nonce-checked, honeypot-checked and rate-limited (one per minute per IP);
2. fires `do_action( 'daimzap_wholesale_application', $data )` with sanitized data;
3. emails the site admin (address filterable via
   `daimzap_wholesale_notification_email`).

A plugin can hook `daimzap_wholesale_application` to create the customer
account, apply a price tier or push the lead into 1С, without touching the theme.

## Useful filters

| Filter                              | Purpose                                  |
|-------------------------------------|------------------------------------------|
| `daimzap_skins`                     | Register or replace skins                 |
| `daimzap_current_skin`              | Force a skin programmatically             |
| `daimzap_vehicle_attributes`        | Attribute slugs, labels and archive bases |
| `daimzap_products_per_page`         | Catalog page size (default 24)            |
| `daimzap_loop_columns`              | Grid columns (default 4)                  |
| `daimzap_summary_specs_limit`       | Spec rows in the product summary          |
| `daimzap_load_font_awesome`         | Disable the icon font                     |
| `daimzap_limit_cart_fragments`      | Keep cart fragments on every page         |
| `daimzap_wholesale_notification_email` | Where applications are emailed         |
| `daimzap_account_orders_per_page`   | Orders per page in My Account             |

## Compatibility

- **HPOS:** order data is read only through the `WC_Order` CRUD API
  (`wc_get_order`, `$order->get_*()`) and the
  `woocommerce_my_account_my_orders_query` filter, so the theme works with
  High-Performance Order Storage without change.
- **SEO plugins:** the theme adds no title, meta or schema output of its own.
  Breadcrumbs defer to `woocommerce_breadcrumb()`, and product structured data
  comes from WooCommerce core.
- **Do not add `woocommerce.php`** to the theme — it would take priority over
  `archive-product.php` and disable the catalog layout.
