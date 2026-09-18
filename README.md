# DaimZap — WordPress/WooCommerce theme

Custom classic theme for **DaimZap** (daimzap.ru), an auto parts catalog in Grozny.
The frontend is built specifically for parts discovery — SKU-first search, vehicle
make/model compatibility filtering, retail and wholesale paths. WooCommerce runs
the commerce engine underneath without showing through in the UI.

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
npm run dev     # watch
npm run build   # minified build into assets/css/main.css
```

Tailwind CSS v4 with a CSS-first config. Design tokens live in
`assets/css/src/main.css` (`@theme`), and component classes are split by area
under `assets/css/src/parts/`. Tailwind utilities are used for layout in markup;
anything that repeats becomes a `dz-` component class.

**Commit the built `assets/css/main.css`** — the theme is deployed by file copy
and must not require a build on the server.

### Structure

```
functions.php          bootstrap only; every feature lives in inc/
inc/
  setup.php            theme supports, menus, image sizes, widget areas
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
assets/                css/src → css/main.css, js/, images/
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
