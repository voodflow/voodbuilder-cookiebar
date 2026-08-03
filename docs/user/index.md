# Vcookiebar — user manual

## What it does

Vcookiebar shows a cookie consent bar and stores visitor preferences. Administrators configure defaults and privacy links from Filament under the **Vcookiebar** navigation group.

## Settings

Open **Vcookiebar → Settings**:

| Field | Purpose |
| --- | --- |
| Enable cookie bar | Master switch for public consent endpoint and runtime |
| Privacy policy URL | Optional link shown next to consent actions |
| Consent cookie name | HTTP cookie that stores the visitor choice |
| Default preferences | Starting state before the visitor chooses (Necessary stays on) |

## Visitor flow

1. On first visit the public banner appears (bottom of the page).
2. The visitor accepts all, rejects optional categories, or customizes toggles.
3. Preferences are posted to `POST /{prefix}/consent` (CSRF + throttle) and stored in a first-party HttpOnly cookie.
4. The page dispatches a `vcookiebar:consent` browser event so tracking scripts (for example VoodBuilder monitoring) can start only after analytics consent.

Necessary cookies cannot be turned off. The banner does not appear again while the consent cookie is present.
