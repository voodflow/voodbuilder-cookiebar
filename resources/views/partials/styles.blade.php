<style>
    .vcookiebar-shell {
        --vcb-panel-bg: #ffffff;
        --vcb-text: #111827;
        --vcb-muted: #374151;
        --vcb-border: rgba(17, 24, 39, 0.12);
        --vcb-btn-bg: #f9fafb;
        --vcb-btn-text: #111827;
        --vcb-btn-primary-bg: #111827;
        --vcb-btn-primary-text: #ffffff;
        --vcb-shadow: 0 12px 40px rgba(15, 23, 42, 0.18);
        --vcb-gap: 1rem;
        --vcb-inset: 1rem;
        font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        color: var(--vcb-text);
        z-index: 2147483000;
        pointer-events: none;
    }

    /* Base: packaged light palette (defaults on .vcookiebar-shell) + dark preset */
    html.dark .vcookiebar-shell--theme-base,
    html.dark .vcookiebar-shell--theme-auto {
        --vcb-panel-bg: #111827;
        --vcb-text: #f9fafb;
        --vcb-muted: #d1d5db;
        --vcb-border: rgba(255, 255, 255, 0.12);
        --vcb-btn-bg: #1f2937;
        --vcb-btn-text: #f9fafb;
        --vcb-btn-primary-bg: #f9fafb;
        --vcb-btn-primary-text: #111827;
        --vcb-shadow: 0 12px 40px rgba(0, 0, 0, 0.45);
    }

    @media (prefers-color-scheme: dark) {
        .vcookiebar-shell--theme-base,
        .vcookiebar-shell--theme-auto {
            --vcb-panel-bg: #111827;
            --vcb-text: #f9fafb;
            --vcb-muted: #d1d5db;
            --vcb-border: rgba(255, 255, 255, 0.12);
            --vcb-btn-bg: #1f2937;
            --vcb-btn-text: #f9fafb;
            --vcb-btn-primary-bg: #f9fafb;
            --vcb-btn-primary-text: #111827;
            --vcb-shadow: 0 12px 40px rgba(0, 0, 0, 0.45);
        }
    }

    /* VoodBuilder: inherit page theme tokens (+ legacy voodflow class) */
    .vcookiebar-shell--theme-voodbuilder,
    .vcookiebar-shell--theme-voodflow {
        --vcb-panel-bg: var(--color-vp-bg-elv, var(--color-vp-bg, #fff));
        --vcb-text: var(--color-vp-text-1, #111827);
        --vcb-muted: var(--color-vp-text-2, #374151);
        --vcb-border: var(--color-vp-divider, rgba(17, 24, 39, 0.12));
        --vcb-btn-bg: var(--color-vp-bg-alt, #f9fafb);
        --vcb-btn-text: var(--color-vp-text-1, #111827);
        --vcb-btn-primary-bg: var(--color-vp-brand-1, #111827);
        --vcb-btn-primary-text: #fff;
    }

    html.dark .vcookiebar-shell--theme-voodbuilder,
    html.dark .vcookiebar-shell--theme-voodflow {
        --vcb-panel-bg: var(--color-vp-bg-elv, #111827);
        --vcb-text: var(--color-vp-text-1, #f9fafb);
        --vcb-muted: var(--color-vp-text-2, #d1d5db);
        --vcb-border: var(--color-vp-divider, rgba(255, 255, 255, 0.12));
        --vcb-btn-bg: var(--color-vp-bg-alt, #1f2937);
        --vcb-btn-text: var(--color-vp-text-1, #f9fafb);
        --vcb-btn-primary-bg: var(--color-vp-brand-1, #f9fafb);
        --vcb-btn-primary-text: var(--color-vp-bg, #111827);
        --vcb-shadow: 0 12px 40px rgba(0, 0, 0, 0.45);
    }

    .vcookiebar {
        pointer-events: none;
    }
    .vcookiebar-shell--bottom .vcookiebar {
        position: fixed;
        inset-inline: 0;
        bottom: 0;
        padding: var(--vcb-inset);
    }
    .vcookiebar-shell--top .vcookiebar {
        position: fixed;
        inset-inline: 0;
        top: 0;
        padding: var(--vcb-inset);
    }
    .vcookiebar-shell--bottom-right .vcookiebar,
    .vcookiebar-shell--bottom-left .vcookiebar {
        position: fixed;
        bottom: var(--vcb-inset);
        padding: 0;
        width: min(28rem, calc(100vw - 2 * var(--vcb-inset)));
    }
    .vcookiebar-shell--bottom-right .vcookiebar { right: var(--vcb-inset); left: auto; }
    .vcookiebar-shell--bottom-left .vcookiebar { left: var(--vcb-inset); right: auto; }

    .vcookiebar__panel {
        pointer-events: auto;
        max-width: 56rem;
        margin: 0 auto;
        padding: 1.25rem 1.5rem;
        border: 1px solid var(--vcb-border);
        border-radius: 0.75rem;
        background: var(--vcb-panel-bg);
        box-shadow: var(--vcb-shadow);
        color: var(--vcb-text);
    }
    .vcookiebar-shell--bottom-right .vcookiebar__panel,
    .vcookiebar-shell--bottom-left .vcookiebar__panel {
        max-width: none;
        margin: 0;
    }
    .vcookiebar__title {
        margin: 0 0 0.35rem;
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.3;
    }
    .vcookiebar__message,
    .vcookiebar__privacy {
        margin: 0;
        font-size: 0.925rem;
        line-height: 1.5;
        color: var(--vcb-muted);
    }
    .vcookiebar__privacy { margin-top: 0.5rem; }
    .vcookiebar__privacy a {
        color: inherit;
        text-decoration: underline;
    }
    .vcookiebar__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    /* Corner placements: keep primary actions on one row */
    .vcookiebar-shell--bottom-right .vcookiebar__actions[data-vcookiebar-actions],
    .vcookiebar-shell--bottom-left .vcookiebar__actions[data-vcookiebar-actions] {
        flex-wrap: nowrap;
        align-items: stretch;
    }
    .vcookiebar-shell--bottom-right .vcookiebar__actions[data-vcookiebar-actions] .vcookiebar__btn,
    .vcookiebar-shell--bottom-left .vcookiebar__actions[data-vcookiebar-actions] .vcookiebar__btn {
        flex: 1 1 0;
        min-width: 0;
        padding: 0.5rem 0.4rem;
        font-size: 0.8rem;
        text-align: center;
        white-space: nowrap;
    }
    .vcookiebar__btn {
        appearance: none;
        border: 1px solid var(--vcb-border);
        border-radius: 0.5rem;
        background: var(--vcb-btn-bg);
        color: var(--vcb-btn-text);
        font: inherit;
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.2;
        padding: 0.55rem 0.9rem;
        cursor: pointer;
    }
    .vcookiebar__btn:hover { filter: brightness(0.97); }
    .vcookiebar__btn:disabled { opacity: 0.6; cursor: wait; }
    .vcookiebar__btn--primary {
        border-color: var(--vcb-btn-primary-bg);
        background: var(--vcb-btn-primary-bg);
        color: var(--vcb-btn-primary-text);
    }
    .vcookiebar__btn--ghost { background: transparent; }
    .vcookiebar__prefs {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--vcb-border);
    }
    .vcookiebar__prefs[hidden] {
        display: none !important;
    }
    .vcookiebar__list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0.75rem;
    }
    .vcookiebar__label {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        cursor: pointer;
    }
    .vcookiebar__label input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        box-sizing: border-box;
        flex: 0 0 auto;
        width: 1.1rem;
        height: 1.1rem;
        margin: 0.2rem 0 0;
        border: 1.5px solid color-mix(in srgb, var(--vcb-btn-primary-bg) 45%, var(--vcb-border));
        border-radius: 0.28rem;
        background: var(--vcb-panel-bg);
        accent-color: var(--vcb-btn-primary-bg);
        cursor: pointer;
        display: grid;
        place-content: center;
        transition: background .12s ease, border-color .12s ease;
    }
    .vcookiebar__label input[type="checkbox"]::before {
        content: "";
        width: 0.62rem;
        height: 0.62rem;
        transform: scale(0);
        transition: transform .12s ease;
        clip-path: polygon(14% 44%, 0 65%, 38% 100%, 100% 16%, 80% 0%, 34% 62%);
        background: var(--vcb-btn-primary-text);
    }
    .vcookiebar__label input[type="checkbox"]:checked {
        background: var(--vcb-btn-primary-bg);
        border-color: var(--vcb-btn-primary-bg);
    }
    .vcookiebar__label input[type="checkbox"]:checked::before {
        transform: scale(1);
    }
    .vcookiebar__label input[type="checkbox"]:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }
    .vcookiebar__label input[type="checkbox"]:focus-visible {
        outline: 2px solid color-mix(in srgb, var(--vcb-btn-primary-bg) 55%, transparent);
        outline-offset: 2px;
    }
    .vcookiebar__label-text {
        display: grid;
        gap: 0.15rem;
        font-size: 0.875rem;
        color: var(--vcb-muted);
    }
    .vcookiebar__label-text strong {
        color: var(--vcb-text);
        font-weight: 600;
    }
    .vcookiebar__status {
        margin: 0.75rem 0 0;
        font-size: 0.85rem;
        color: #b91c1c;
    }
    .vcookiebar__reopen {
        pointer-events: auto;
        position: fixed !important;
        z-index: 2147483001;
        inset: auto !important;
        top: auto !important;
        left: auto !important;
        right: max(0.85rem, env(safe-area-inset-right, 0px)) !important;
        bottom: max(0.85rem, env(safe-area-inset-bottom, 0px)) !important;
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--vcb-border) 80%, transparent);
        background: color-mix(in srgb, var(--vcb-panel-bg) 72%, transparent);
        color: var(--vcb-muted);
        opacity: 0.38;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: none;
        backdrop-filter: blur(4px);
        transition: opacity .18s ease, color .18s ease, border-color .18s ease, background .18s ease, transform .18s ease;
    }
    .vcookiebar__reopen[hidden] {
        display: none !important;
    }
    .vcookiebar__reopen:hover,
    .vcookiebar__reopen:focus-visible {
        opacity: 0.9;
        color: var(--vcb-btn-primary-bg);
        border-color: color-mix(in srgb, var(--vcb-btn-primary-bg) 40%, var(--vcb-border));
        background: var(--vcb-panel-bg);
        transform: none;
        outline: none;
    }
</style>
