# Vcookiebar — developer guide

Composer: `voodflow/vcookiebar` · Namespace: `Voodflow\Vcookiebar` · Plugin id: `vcookiebar`

For the full operator-facing detail and payload tables, see also [developer/index.md](index.md) (kept in sync with this guide).

## Integration

```bash
composer require voodflow/vcookiebar
php artisan vendor:publish --tag=vcookiebar-config
```

```php
use Voodflow\Vcookiebar\VcookiebarPlugin;

$panel->plugins([VcookiebarPlugin::make()]);
```

Public consent routes load when `vcookiebar.enabled` is true even without the Filament plugin.

## Service provider & plugin

| Class | Role |
|-------|------|
| `VcookiebarServiceProvider` | Config, views, routes, auto-inject |
| `VcookiebarPlugin` | Filament settings page |

## Extension points

- `Vcookiebar` — enablement / category helpers
- `Support\Banner` — visibility + runtime config
- `Support\ConsentPayload` — normalize / encode / decode
- `Support\SettingsStore` — admin persistence (cache-backed)
- Blade: `<x-vcookiebar::banner />`, `<x-vcookiebar::gated category="analytics">`

## Events (browser)

`vcookiebar:consent` with `{ preferences }`. Helpers: `window.__vcookiebar.has()`, `window.__vcookiebar.open()`.

## Config & assets

Publish `config/vcookiebar.php`. Views under `resources/views`. No dedicated Vite build required for core banner.

## Companions

- **Voodbuilder**: analytics scripts gated on `analytics` consent; policy link fields reuse builder link types when available
- Suggest only — package remains usable alone

## Do / don't

- **Do** keep CSRF + throttle on consent; never trust client-only category lists
- **Do** force `necessary` to true server-side
- **Don't** store PII in the consent cookie
- **Don't** nest admin under another product’s settings screen
