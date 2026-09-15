# Vcookiebar — user manual

## What it does

Vcookiebar shows a cookie consent bar and stores visitor preferences. Administrators configure defaults, which categories to show, policy links, and appearance from Filament under the **Vcookiebar** navigation group.

## Settings

Open **Vcookiebar → Settings**:

| Field | Purpose |
| --- | --- |
| Enable cookie bar | Master switch for public consent endpoint and runtime |
| Privacy / cookie policy | Optional links (internal page, site path, or external URL — same style as Voodbuilder button links when available) |
| Consent cookie name | HTTP cookie that stores the visitor choice |
| Categories to show | Which optional categories appear under Customize (Necessary always shown) |
| Default preferences | Starting state before the visitor chooses (Necessary stays on) |
| Placement | Bottom, bottom-right / bottom-left (floating inset), or top |
| Theme | Base (built-in light/dark), VoodBuilder (page theme, if installed), or custom colors |
| Reopen icon | Subtle control after consent to change preferences |

## Visitor flow

1. On first visit the public banner appears (placement from settings).
2. The visitor accepts all, rejects optional categories, or customizes toggles.
3. Preferences are posted to `POST /{prefix}/consent` (CSRF + throttle) and stored in a first-party HttpOnly cookie.
4. The page dispatches a `vcookiebar:consent` browser event and unlocks gated scripts.
5. A small reopen icon stays available (if enabled) so preferences can be changed later.

Necessary cookies cannot be turned off.

## Blocking tracking

- **With Voodbuilder:** scripts in Settings → Analytics load only after **analytics** consent.
- **Standalone:** mark scripts with `data-vcookiebar="analytics"` (or `marketing` / `preferences`) and `type="text/plain"`, or wrap them in `<x-vcookiebar::gated category="analytics">`.
