# Vcookiebar — developer manual

## Package identity

| Item | Value |
| --- | --- |
| Composer | `voodflow/vcookiebar` |
| Namespace | `Voodflow\Vcookiebar` |
| Config | `config/vcookiebar.php` |
| Filament plugin id | `vcookiebar` |
| Navigation group | Own group (`Vcookiebar`), not under any page builder |

The package is intentionally agnostic: it does not require a page builder or other Voodflow plugins.

## Install

```bash
composer require voodflow/vcookiebar
php artisan vendor:publish --tag=vcookiebar-config
```

Register on a Filament panel:

```php
use Voodflow\Vcookiebar\VcookiebarPlugin;

$panel->plugins([
    VcookiebarPlugin::make(),
]);
```

## Public banner

Include the banner manually:

```blade
<x-vcookiebar::banner />
```

Or rely on **auto-inject** (`vcookiebar.auto_inject`, default `true`), which pushes the banner onto the `overlays` stack of configured views (VoodBuilder layouts by default). Set `auto_inject` to `false` on standalone hosts that render the component themselves.

The banner:

- Renders only when the package is enabled and no consent cookie is present
- Posts JSON to the consent endpoint with `X-CSRF-TOKEN`
- Sets `window.__vcookiebar.preferences` and dispatches `vcookiebar:consent` with `{ preferences }`

Listen for consent without coupling to Filament:

```js
window.addEventListener('vcookiebar:consent', (event) => {
    const preferences = event.detail?.preferences;
    if (preferences?.analytics) {
        // load analytics
    }
});
```

## Routes

When `vcookiebar.enabled` is true:

| Method | Path | Name |
| --- | --- | --- |
| `POST` | `/{route_prefix}/consent` | `vcookiebar.consent.store` |

Default prefix: `vcookiebar`. Middleware: `web` + throttle (`vcookiebar.consent_throttle` requests/minute).

### Consent payload

```json
{
  "preferences": {
    "necessary": true,
    "preferences": false,
    "analytics": true,
    "marketing": false
  }
}
```

Security rules:

- CSRF required (`web` middleware)
- Only configured category keys accepted
- Non-boolean values rejected
- `necessary` is always forced to `true`
- Consent cookie is `HttpOnly`, `SameSite=Lax`, `Secure` when the request is HTTPS

## Config keys

See `config/vcookiebar.php`. Environment overrides:

- `VCOOKIEBAR_ENABLED`
- `VCOOKIEBAR_ROUTE_PREFIX`
- `VCOOKIEBAR_CONSENT_THROTTLE`
- `VCOOKIEBAR_PRIVACY_POLICY_URL`
- `VCOOKIEBAR_CONSENT_COOKIE`
- `VCOOKIEBAR_CONSENT_LIFETIME`
- `VCOOKIEBAR_BANNER_ENABLED`
- `VCOOKIEBAR_AUTO_INJECT`

Admin settings are layered via `SettingsStore` (application cache) over config defaults and hydrated on boot.

## Extension points

- `Voodflow\Vcookiebar\Vcookiebar` — enablement and category helpers
- `Voodflow\Vcookiebar\Support\Banner` — public banner visibility + runtime config
- `Voodflow\Vcookiebar\Support\ConsentPayload` — normalize / encode / decode
- `Voodflow\Vcookiebar\Support\SettingsStore` — admin persistence
- `Voodflow\Vcookiebar\Filament\Pages\VcookiebarSettingsPage` — Filament settings

Optional page-builder integration can be added later without coupling this core package.
