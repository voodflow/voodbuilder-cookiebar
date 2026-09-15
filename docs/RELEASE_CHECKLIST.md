# Vcookiebar — release checklist

## Missing for release

- [ ] Packagist listing + version tag (`0.2.0`)
- [ ] Smoke test Filament settings + public banner on a clean Laravel 13 + Filament 5 host
- [ ] Confirm Plumb scan after first Packagist publish (expect 100 with SECURITY + Dependabot cooldown + lean dist)

## Plumb readiness (local)

Mirror of [voodflow/vmedia](https://github.com/voodflow) packaging:

| Check | Status |
|-------|--------|
| `SECURITY.md` | Present |
| `.github/dependabot.yml` + `cooldown` (≥3 days) | Present |
| `composer.lock` excluded from dist (`.gitattributes`) | Present |
| Dist lean (tests / `.github` / `.cursor` / docs / phpunit export-ignore) | Present |
| No unpinned GitHub Actions workflows | N/A (no workflows) |
| PHP `^8.4` (includes current 8.5) | Present |
| Laravel via `illuminate/contracts` `^12\|^13` | Present |
| MIT `LICENSE` | Present |

Verify archive locally:

```bash
git archive --format=tar HEAD | tar -t | head -100
# must NOT list tests/, .github/, composer.lock, .cursor/, phpunit.xml.dist
```

## Test status

Run inside the PHP 8.4 app container:

```bash
cd packages/voodflow/vcookiebar && ./vendor/bin/phpunit
```

## Security & vulnerability review

| Area | Finding | Severity |
|------|---------|----------|
| Authz | Admin via Filament panel auth; no public admin routes | OK |
| Consent POST | CSRF (`web`) + throttle; category allow-list; `necessary` forced true | OK |
| Cookie | HttpOnly, SameSite=Lax, Secure on HTTPS | OK |
| Withdrawal | Reload after save + cleanup patterns for declined categories | OK |
| XSS | Public banner uses escaped Blade | OK |
| Secrets | None stored by package | OK |
