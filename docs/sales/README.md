# Vcookiebar — product overview

Cookie consent for Laravel and Filament 5 sites. Visitors choose preferences; admins configure categories, policy links, and appearance from Filament. Works **standalone** — no page builder required.

## Who it is for

- Marketing and legal teams that need a GDPR-style consent bar
- Product sites that gate analytics/marketing scripts until consent
- Cosmolab / Voodflow hosts that want consent without coupling to the visual editor

## Key features

- Accept all / reject optional / customize categories (Necessary always on)
- Filament settings: enablement, categories, defaults, placement, theme, reopen icon
- Policy links (internal page, path, or external URL)
- First-party HttpOnly consent cookie + CSRF-protected endpoint
- Script gating via `data-vcookiebar` attributes or Blade component
- Optional auto-inject into known public layouts
- Soft integration with Voodbuilder analytics when that package is present

## High-level requirements

| Requirement | Value |
|-------------|--------|
| PHP | 8.4+ |
| Laravel | 12 or 13 |
| Filament | 5 |
| Page builder | Optional |

## Fit in Cosmolab / Voodflow

Own Filament navigation group and routes. Complements site chrome (Vpress/Voodbuilder) and analytics companions without hard dependencies. Ideal listing item for compliance-minded storefronts.

## Learn more

- [User manual](../user/index.md) — operators
- [Developer manual](../developer/README.md) — integration
