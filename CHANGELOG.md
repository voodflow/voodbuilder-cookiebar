## [0.2.3] - 2026-09-25

### Changed

- Package health: security policy, Dependabot with update cooldown, pinned GitHub Actions, lean Composer dist (`export-ignore`)
- Laravel Pint added (`composer format`) and applied — formatting only, no behaviour change

## [0.2.2] - 2026-09-16

### Added

- Anonymous `visitor_id` in consent cookie payload (v2) and `VisitorConsentSaved` event for companions (e.g. VoodPrivacy per-visitor evidence)

### Changed

- Consent cookie encode/decode preserves visitor id across preference updates

## [0.2.1] - 2026-09-16

### Changed

- Remove in-repo work documentation (checklists, sales/developer stubs, internal notes); keep public README assets under `docs/images/` (and product academy/manual where applicable)


# Changelog

All notable changes to `voodflow/vcookiebar` are documented in this file.

## [0.2.0] - 2026-09-15

### Added

- Filament settings tabs: General (enablement, appearance, categories), Policies, Banner texts
- Banner texts follow SitePage / Vtuts workflow: primary configuration + **Translate** / delete derived locales (site locales as candidates)
- Per-locale runtime copy resolution with fallback to primary, then packaged lang
- GDPR hardening: page reload after consent save, client-side cleanup of known analytics/marketing cookies when a category is declined, optional categories forced off (no pre-ticked opt-in)
- Configurable `cleanup_cookies` patterns in `config/vcookiebar.php`
- Policy site-page links resolve the visitor locale via translation groups
- MIT release packaging: `SECURITY.md`, Dependabot cooldown, lean Composer dist via `.gitattributes`

### Changed

- Color themes: **Base** (built-in light/dark), **VoodBuilder** (page tokens, option only if builder installed), **Custom** — replaces confusing “Voodflow theme” / system auto labels (`auto`→`base`, `voodflow`→`voodbuilder`)
- Settings UI uses tabs + two-column layout instead of stacked full-width sections
- Category “Default on” controls removed from the UI (opt-in only)
- Banner texts no longer pre-create empty tabs for every site locale

## [0.1.1] - 2026-08-25

### Added

- Initial public consent banner, Filament settings page, script gating, and auto-inject into known layouts
