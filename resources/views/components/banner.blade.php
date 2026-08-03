@if (\Voodflow\Vcookiebar\Support\Banner::shouldRender())
@php
    $config = \Voodflow\Vcookiebar\Support\Banner::runtimeConfig();
@endphp

<aside
    id="vcookiebar-root"
    class="vcookiebar"
    role="dialog"
    aria-modal="false"
    aria-labelledby="vcookiebar-title"
    aria-describedby="vcookiebar-message"
    data-vcookiebar
    hidden
>
    <div class="vcookiebar__panel">
        <div class="vcookiebar__copy">
            <h2 id="vcookiebar-title" class="vcookiebar__title">{{ $config['copy']['title'] }}</h2>
            <p id="vcookiebar-message" class="vcookiebar__message">{{ $config['copy']['message'] }}</p>
            @if ($config['privacyPolicyUrl'])
                <p class="vcookiebar__privacy">
                    <a href="{{ $config['privacyPolicyUrl'] }}" rel="noopener noreferrer" target="_blank">
                        {{ $config['copy']['privacy'] }}
                    </a>
                </p>
            @endif
        </div>

        <div class="vcookiebar__actions" data-vcookiebar-actions>
            <button type="button" class="vcookiebar__btn vcookiebar__btn--primary" data-vcookiebar-accept>
                {{ $config['copy']['acceptAll'] }}
            </button>
            <button type="button" class="vcookiebar__btn" data-vcookiebar-reject>
                {{ $config['copy']['rejectOptional'] }}
            </button>
            <button type="button" class="vcookiebar__btn vcookiebar__btn--ghost" data-vcookiebar-customize>
                {{ $config['copy']['customize'] }}
            </button>
        </div>

        <form class="vcookiebar__prefs" data-vcookiebar-prefs hidden>
            <ul class="vcookiebar__list">
                @foreach ($config['categories'] as $category)
                    <li class="vcookiebar__item">
                        <label class="vcookiebar__label">
                            <input
                                type="checkbox"
                                name="preferences[{{ $category['key'] }}]"
                                value="1"
                                data-vcookiebar-category="{{ $category['key'] }}"
                                @checked($config['preferences'][$category['key']] ?? false)
                                @disabled($category['locked'])
                            >
                            <span class="vcookiebar__label-text">
                                <strong>{{ $category['label'] }}</strong>
                                <span>{{ $category['description'] }}</span>
                            </span>
                        </label>
                    </li>
                @endforeach
            </ul>
            <div class="vcookiebar__actions">
                <button type="submit" class="vcookiebar__btn vcookiebar__btn--primary">
                    {{ $config['copy']['save'] }}
                </button>
            </div>
        </form>

        <p class="vcookiebar__status" data-vcookiebar-status role="status" hidden></p>
    </div>
</aside>

<style>
    .vcookiebar {
        position: fixed;
        inset-inline: 0;
        bottom: 0;
        z-index: 2147483000;
        padding: 1rem;
        font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        color: #111827;
        pointer-events: none;
    }
    .vcookiebar__panel {
        pointer-events: auto;
        max-width: 56rem;
        margin: 0 auto;
        padding: 1.25rem 1.5rem;
        border: 1px solid rgba(17, 24, 39, 0.12);
        border-radius: 0.75rem;
        background: #fff;
        box-shadow: 0 12px 40px rgba(15, 23, 42, 0.18);
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
        color: #374151;
    }
    .vcookiebar__privacy {
        margin-top: 0.5rem;
    }
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
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        background: #f9fafb;
        color: #111827;
        font: inherit;
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.2;
        padding: 0.55rem 0.9rem;
        cursor: pointer;
    }
    .vcookiebar__btn:hover {
        background: #f3f4f6;
    }
    .vcookiebar__btn:disabled {
        opacity: 0.6;
        cursor: wait;
    }
    .vcookiebar__btn--primary {
        border-color: #111827;
        background: #111827;
        color: #fff;
    }
    .vcookiebar__btn--primary:hover {
        background: #000;
    }
    .vcookiebar__btn--ghost {
        background: transparent;
    }
    .vcookiebar__prefs {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(17, 24, 39, 0.08);
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
    .vcookiebar__label input {
        margin-top: 0.2rem;
    }
    .vcookiebar__label-text {
        display: grid;
        gap: 0.15rem;
        font-size: 0.875rem;
        color: #4b5563;
    }
    .vcookiebar__label-text strong {
        color: #111827;
        font-weight: 600;
    }
    .vcookiebar__status {
        margin: 0.75rem 0 0;
        font-size: 0.85rem;
        color: #b91c1c;
    }
    @media (prefers-color-scheme: dark) {
        .vcookiebar__panel {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.12);
            color: #f9fafb;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.45);
        }
        .vcookiebar__message,
        .vcookiebar__privacy,
        .vcookiebar__label-text {
            color: #d1d5db;
        }
        .vcookiebar__label-text strong {
            color: #f9fafb;
        }
        .vcookiebar__btn {
            background: #1f2937;
            border-color: #374151;
            color: #f9fafb;
        }
        .vcookiebar__btn:hover {
            background: #374151;
        }
        .vcookiebar__btn--primary {
            background: #f9fafb;
            border-color: #f9fafb;
            color: #111827;
        }
        .vcookiebar__btn--primary:hover {
            background: #e5e7eb;
        }
        .vcookiebar__prefs {
            border-top-color: rgba(255, 255, 255, 0.1);
        }
    }
</style>

<script>
(function () {
    const root = document.getElementById('vcookiebar-root');
    if (! root) {
        return;
    }

    const config = @json($config);
    const prefsForm = root.querySelector('[data-vcookiebar-prefs]');
    const statusEl = root.querySelector('[data-vcookiebar-status]');
    const buttons = root.querySelectorAll('button');

    window.__vcookiebar = window.__vcookiebar || {};
    window.__vcookiebar.preferences = window.__vcookiebar.preferences || null;

    function setBusy(busy) {
        buttons.forEach(function (button) {
            button.disabled = busy;
        });
    }

    function showError(message) {
        if (! statusEl) {
            return;
        }
        statusEl.hidden = false;
        statusEl.textContent = message || config.copy.error;
    }

    function hideError() {
        if (! statusEl) {
            return;
        }
        statusEl.hidden = true;
        statusEl.textContent = '';
    }

    function readPreferencesFromForm() {
        const preferences = Object.assign({}, config.preferences);

        root.querySelectorAll('[data-vcookiebar-category]').forEach(function (input) {
            const key = input.getAttribute('data-vcookiebar-category');
            if (! key) {
                return;
            }
            preferences[key] = key === 'necessary' ? true : Boolean(input.checked);
        });

        preferences.necessary = true;

        return preferences;
    }

    function acceptAll() {
        const preferences = Object.assign({}, config.preferences);
        Object.keys(preferences).forEach(function (key) {
            preferences[key] = true;
        });
        preferences.necessary = true;
        return preferences;
    }

    function rejectOptional() {
        const preferences = Object.assign({}, config.preferences);
        Object.keys(preferences).forEach(function (key) {
            preferences[key] = key === 'necessary';
        });
        return preferences;
    }

    function publishConsent(preferences) {
        window.__vcookiebar = window.__vcookiebar || {};
        window.__vcookiebar.preferences = preferences;
        window.dispatchEvent(new CustomEvent('vcookiebar:consent', {
            detail: { preferences: preferences },
        }));
    }

    function submitPreferences(preferences) {
        hideError();
        setBusy(true);

        return fetch(config.endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': config.csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ preferences: preferences }),
        }).then(function (response) {
            if (! response.ok) {
                throw new Error('consent_failed');
            }
            return response.json();
        }).then(function (payload) {
            const saved = payload && payload.preferences ? payload.preferences : preferences;
            publishConsent(saved);
            root.remove();
        }).catch(function () {
            showError(config.copy.error);
            setBusy(false);
        });
    }

    root.querySelector('[data-vcookiebar-accept]')?.addEventListener('click', function () {
        submitPreferences(acceptAll());
    });

    root.querySelector('[data-vcookiebar-reject]')?.addEventListener('click', function () {
        submitPreferences(rejectOptional());
    });

    root.querySelector('[data-vcookiebar-customize]')?.addEventListener('click', function () {
        if (! prefsForm) {
            return;
        }
        prefsForm.hidden = ! prefsForm.hidden;
    });

    prefsForm?.addEventListener('submit', function (event) {
        event.preventDefault();
        submitPreferences(readPreferencesFromForm());
    });

    root.hidden = false;
})();
</script>
@endif
