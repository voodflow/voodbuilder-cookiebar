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

    /* Voodflow / Voodbuilder theme tokens */
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

    html.dark .vcookiebar-shell--theme-voodflow,
    html.dark .vcookiebar-shell--theme-auto {
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

    @media (prefers-color-scheme: dark) {
        .vcookiebar-shell--theme-auto:not(.vcookiebar-shell--theme-voodflow) {
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
        width: min(24rem, calc(100vw - 2 * var(--vcb-inset)));
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
    .vcookiebar__label input { margin-top: 0.2rem; }
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
        position: fixed;
        z-index: 2147483001;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 999px;
        border: 1px solid var(--vcb-border);
        background: color-mix(in srgb, var(--vcb-panel-bg) 88%, transparent);
        color: var(--vcb-muted);
        opacity: 0.55;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.12);
        transition: opacity .15s ease, transform .15s ease;
    }
    .vcookiebar__reopen:hover,
    .vcookiebar__reopen:focus-visible {
        opacity: 0.95;
        transform: translateY(-1px);
        outline: none;
    }
    .vcookiebar-shell--bottom .vcookiebar__reopen,
    .vcookiebar-shell--bottom-right .vcookiebar__reopen { right: var(--vcb-inset); bottom: var(--vcb-inset); }
    .vcookiebar-shell--bottom-left .vcookiebar__reopen { left: var(--vcb-inset); bottom: var(--vcb-inset); }
    .vcookiebar-shell--top .vcookiebar__reopen { right: var(--vcb-inset); top: var(--vcb-inset); }
</style>
