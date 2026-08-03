# Vookiebar — developer manual

## Package identity

| Item | Value |
| --- | --- |
| Composer | `voodflow/vookiebar` |
| Namespace | `Voodflow\Vookiebar` |
| Config | `config/vookiebar.php` |
| Filament plugin id | `vookiebar` |
| Navigation group | Own group (`Vookiebar`), not under any page builder |

The package is intentionally agnostic: it does not require a page builder or other Voodflow plugins.

## Install

```bash
composer require voodflow/vookiebar
php artisan vendor:publish --tag=vookiebar-config
```

Register on a Filament panel:

```php
use Voodflow\Vookiebar\VookiebarPlugin;

$panel->plugins([
    VookiebarPlugin::make(),
]);
```

## Routes

When `vookiebar.enabled` is true:

| Method | Path | Name |
| --- | --- | --- |
| `POST` | `/{route_prefix}/consent` | `vookiebar.consent.store` |

Default prefix: `vookiebar`. Middleware: `web` + throttle (`vookiebar.consent_throttle` requests/minute).

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

See `config/vookiebar.php`. Environment overrides:

- `VOOKIEBAR_ENABLED`
- `VOOKIEBAR_ROUTE_PREFIX`
- `VOOKIEBAR_CONSENT_THROTTLE`
- `VOOKIEBAR_PRIVACY_POLICY_URL`
- `VOOKIEBAR_CONSENT_COOKIE`
- `VOOKIEBAR_CONSENT_LIFETIME`

Admin settings are layered via `SettingsStore` (application cache) over config defaults.

## Extension points

- `Voodflow\Vookiebar\Vookiebar` — enablement and category helpers
- `Voodflow\Vookiebar\Support\ConsentPayload` — normalize / encode / decode
- `Voodflow\Vookiebar\Support\SettingsStore` — admin persistence
- `Voodflow\Vookiebar\Filament\Pages\VookiebarSettingsPage` — Filament settings

Optional page-builder integration can be added later without coupling this core package.
