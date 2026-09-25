# VoodCookie (`voodflow/vcookiebar`)

<img class="filament-hidden" src="https://raw.githubusercontent.com/voodflow/voodflow-public-docs/main/vcookiebar/images/promo.png" alt="VoodCookie — Filament Cookie Consent by VoodFlow" />

**Cookie consent** for Laravel + Filament 5: opt-in preferences UI, real script gating, and a Filament settings page. Works **standalone** in any Filament app — no page builder required.

MIT.

## Features

- **Automatic integration** — banner auto-injects into known public layouts, or include `<x-vcookiebar::banner />` yourself
- **Settings page** — enablement, categories, appearance, and per-locale banner texts + policy links from Filament
- **GDPR-oriented consent** — accept all / reject optional / customize; necessary always on; optional categories never pre-ticked
- **Real blocking** — scripts stay inert until the visitor grants the matching category; known analytics/marketing cookies are cleared when a category is declined; the page reloads after consent so revoked scripts unload
- **Themes** — **Base** (built-in light/dark), **VoodBuilder** (page palette, only if the builder is installed), **Custom** (color pickers)
- **Highly configurable** — placement, reopen control, cookie name, cleanup patterns
- **Responsive** — corner or full-width layouts; works on desktop and mobile
- **Multi-language** — packaged EN / IT / DE / ES / FR; Filament uses a primary configuration plus derived translations (**Translate** action), following site locales

## Requirements

| Requirement | Notes |
|-------------|--------|
| PHP | 8.4+ |
| Laravel | 12 or 13 |
| Filament | 5 |

## Install

```bash
composer require voodflow/vcookiebar
php artisan vendor:publish --tag=vcookiebar-config
```

Register the plugin on your Filament panel:

```php
use Voodflow\Vcookiebar\VcookiebarPlugin;

$panel->plugins([
    VcookiebarPlugin::make(),
]);
```

Omitting `VcookiebarPlugin` hides the admin pages. Public consent routes still load while `vcookiebar.enabled` is true.

Optional: publish translations to customize packaged strings outside Filament:

```bash
php artisan vendor:publish --tag=vcookiebar-translations
```

## Activate on the public site

### Auto-inject (default)

With `vcookiebar.auto_inject` = `true` (default), the package pushes the banner onto the `overlays` stack of configured views (`vcookiebar.auto_inject_views`). Out of the box those entries target common VoodBuilder layouts; if those views are not present, nothing breaks — simply include the banner manually.

### Manual include (any Laravel layout)

```blade
{{-- typically near the end of <body> --}}
<x-vcookiebar::banner />
```

Set `VCOOKIEBAR_AUTO_INJECT=false` when you always include the component yourself.

## Configure (Filament)

Open **Vcookiebar → Settings**:

| Tab | What you set |
|-----|----------------|
| **General** | Master enable, consent cookie name, placement / theme / colors, reopen icon, which categories appear under Customize |
| **Banner texts** | Primary copy + **policy links** per language, then **Translate** to derive other site languages. Blank text fields keep the packaged translation. Site-page policy targets still follow the visitor locale |

Visitor flow:

1. First visit shows the banner (no consent cookie yet).
2. Accept all / reject optional / customize → `POST /vcookiebar/consent` (CSRF + throttle).
3. Preferences are stored in a first-party HttpOnly cookie; the page reloads.
4. Gated scripts for granted categories activate; declined categories clear configured cookie names.
5. Reopen control (if enabled) lets visitors change preferences later.

## Gate scripts and cookies (required for real compliance)

The banner alone does not invent tracking. **You must not load non-essential scripts until consent.** Mark them so the runtime can unlock them:

```html
<script type="text/plain" data-vcookiebar="analytics" src="https://www.googletagmanager.com/gtag/js?id=G-XXXX"></script>
<script type="text/plain" data-vcookiebar="marketing">
  // inline marketing pixel
</script>
```

Or wrap markup:

```blade
<x-vcookiebar::gated category="analytics">
    <script src="https://www.googletagmanager.com/gtag/js?id=G-XXXX"></script>
</x-vcookiebar::gated>
```

Listen without coupling to Filament:

```js
window.addEventListener('vcookiebar:consent', (event) => {
    if (event.detail?.preferences?.analytics) {
        // load analytics if you prefer an event-driven approach
    }
});

window.__vcookiebar.has('analytics'); // boolean
window.__vcookiebar.open();           // reopen the dialog
```

Categories: `necessary` (always on), `preferences`, `analytics`, `marketing`.

Cookie cleanup patterns live under `vcookiebar.cleanup_cookies` (client-readable cookies only; HttpOnly cookies cannot be cleared from JavaScript). Extend the lists for your vendors.

## Standalone Laravel / Filament (no page builder)

1. `composer require` + register `VcookiebarPlugin`
2. Publish config; set policy URLs or configure them in Filament
3. Add `<x-vcookiebar::banner />` to your public layout (or point `auto_inject_views` at your layout name)
4. Gate every analytics / marketing / preference script as above

That is the complete integration. The package does not depend on VoodBuilder, Voodflow, or any other companion.

## Optional: VoodBuilder

If you also use [VoodBuilder](https://voodflow.com), analytics scripts configured in the builder settings wait for **analytics** consent automatically, policy link fields can pick site pages / menu items, and the **VoodBuilder** color theme inherits the page palette. Without VoodBuilder installed, none of that is required — Vcookiebar stays fully usable on its own (**Base** or **Custom** themes).

## Config highlights

Publish `config/vcookiebar.php`. Useful keys / env:

| Key / env | Purpose |
|-----------|---------|
| `enabled` / `VCOOKIEBAR_ENABLED` | Master switch |
| `route_prefix` / `VCOOKIEBAR_ROUTE_PREFIX` | Consent route prefix (`vcookiebar`) |
| `consent_cookie` / `VCOOKIEBAR_CONSENT_COOKIE` | Preference cookie name |
| `consent_lifetime_minutes` | Consent cookie lifetime (default ~1 year) |
| `auto_inject` / `VCOOKIEBAR_AUTO_INJECT` | Push banner into configured views |
| `auto_inject_views` | View names that receive the banner |
| `cleanup_cookies` | Names / prefixes to expire when a category is off |
| `appearance.theme` | `base` \| `voodbuilder` \| `custom` (legacy: `auto`→`base`, `voodflow`→`voodbuilder`) |
| `appearance.*` | Placement, colors, reopen icon |

Admin choices are layered over config via a cache-backed settings store (no extra database table).

## Security notes

- Consent endpoint: `web` middleware (CSRF) + throttle
- Only configured category keys accepted; `necessary` forced `true` server-side
- Consent cookie: HttpOnly, SameSite=Lax, Secure on HTTPS
- Do not store PII in the consent cookie

See [SECURITY.md](SECURITY.md) to report vulnerabilities.

## Testing

```bash
composer test
# or
./vendor/bin/phpunit
```

## License

MIT — see [LICENSE](LICENSE). Trademark note: [NOTICE](NOTICE).

## Changelog

See [CHANGELOG.md](CHANGELOG.md).
