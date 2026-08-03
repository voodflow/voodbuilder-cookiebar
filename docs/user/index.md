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

1. The site loads the consent bar (frontend assets land in a later release).
2. The visitor accepts or customizes categories.
3. Preferences are posted to the consent endpoint and stored in a first-party cookie.

Necessary cookies cannot be turned off.
