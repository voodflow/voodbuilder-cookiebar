# Vcookiebar — release checklist

## Missing for release

- [ ] Packagist / private Composer registry listing + version tag
- [ ] Public changelog entry for the shipping version
- [ ] Confirm LICENSE / commercial terms on the storefront
- [ ] Smoke test Filament settings + public banner on a clean Laravel 13 + Filament 5 host

## Nice-to-have

- [ ] Optional Voodbuilder companion blocks package (suggest only today)
- [ ] Multilingual admin strings beyond host locale
- [ ] Consent audit log / export for enterprise buyers

## Test status

**Result (2026-08-25, Docker PHP 8.4 / package phpunit|pest):** PASS

24 tests, 83 assertions.

## Code quality vs Filament 5

- Uses Filament 5 plugin + Pages API; settings page is package-owned
- Livewire 4 / Laravel 13 contracts in `composer.json`
- No legacy `Filament\Tables\Actions` namespaces observed in core paths

## Security & vulnerability review

| Area | Finding | Severity |
|------|---------|----------|
| Authz | Admin via Filament panel auth; no public admin routes | OK |
| Consent POST | CSRF (`web`) + throttle; category allow-list; `necessary` forced true | OK |
| Cookie | HttpOnly, SameSite=Lax, Secure on HTTPS | OK |
| XSS | Admin HtmlString for static help copy; public banner uses escaped Blade | OK |
| SSRF / uploads / SQLi | N/A (no remote fetch, uploads, or raw SQL) | OK |
| Secrets | None stored by package | OK |
| Mass assignment | Settings via dedicated store, not Eloquent mass-assign of request | OK |

**Critical fixes applied this audit:** none required.
