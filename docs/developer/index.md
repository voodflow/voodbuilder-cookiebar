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

- `VOOKIEBAR_ENABLED`
- `VOOKIEBAR_ROUTE_PREFIX`
- `VOOKIEBAR_CONSENT_THROTTLE`
- `VOOKIEBAR_PRIVACY_POLICY_URL`
- `VOOKIEBAR_CONSENT_COOKIE`
- `VOOKIEBAR_CONSENT_LIFETIME`

Admin settings are layered via `SettingsStore` (application cache) over config defaults.

## Extension points

- `Voodflow\Vcookiebar\Vcookiebar` — enablement and category helpers
- `Voodflow\Vcookiebar\Support\ConsentPayload` — normalize / encode / decode
- `Voodflow\Vcookiebar\Support\SettingsStore` — admin persistence
- `Voodflow\Vcookiebar\Filament\Pages\VcookiebarSettingsPage` — Filament settings

Optional page-builder integration can be added later without coupling this core package.
