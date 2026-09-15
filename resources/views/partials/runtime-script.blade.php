<script>
(function () {
    const shell = document.querySelector('[data-vcookiebar-shell]');
    if (! shell) {
        return;
    }

    const config = @json($config);
    const root = document.getElementById('vcookiebar-root');
    const prefsForm = root ? root.querySelector('[data-vcookiebar-prefs]') : null;
    const statusEl = root ? root.querySelector('[data-vcookiebar-status]') : null;
    const reopenBtn = shell.querySelector('[data-vcookiebar-reopen]');
    // Detach from transformed ancestors so fixed = viewport bottom-right.
    if (reopenBtn && reopenBtn.parentElement !== document.body) {
        document.body.appendChild(reopenBtn);
    }
    const buttons = root ? root.querySelectorAll('button') : [];

    window.__vcookiebar = window.__vcookiebar || {};
    if (config.savedPreferences) {
        window.__vcookiebar.preferences = config.savedPreferences;
    } else {
        window.__vcookiebar.preferences = window.__vcookiebar.preferences || null;
    }

    function setBusy(busy) {
        buttons.forEach(function (button) {
            button.disabled = busy;
        });
    }

    function showError(message) {
        if (! statusEl) return;
        statusEl.hidden = false;
        statusEl.textContent = message || config.copy.error;
    }

    function hideError() {
        if (! statusEl) return;
        statusEl.hidden = true;
        statusEl.textContent = '';
    }

    function readPreferencesFromForm() {
        const preferences = Object.assign({}, config.preferences);
        if (! root) return preferences;
        root.querySelectorAll('[data-vcookiebar-category]').forEach(function (input) {
            const key = input.getAttribute('data-vcookiebar-category');
            if (! key) return;
            preferences[key] = key === 'necessary' ? true : Boolean(input.checked);
        });
        preferences.necessary = true;
        return preferences;
    }

    function acceptAll() {
        const preferences = Object.assign({}, config.preferences);
        Object.keys(preferences).forEach(function (key) { preferences[key] = true; });
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

    function syncFormFromPreferences(preferences) {
        if (! root || ! preferences) return;
        root.querySelectorAll('[data-vcookiebar-category]').forEach(function (input) {
            const key = input.getAttribute('data-vcookiebar-category');
            if (! key || input.disabled) return;
            input.checked = Boolean(preferences[key]);
        });
    }

    function cookieMatches(pattern, name) {
        if (pattern.slice(-1) === '*') {
            return name.indexOf(pattern.slice(0, -1)) === 0;
        }

        return name === pattern;
    }

    function expireCookie(name) {
        const expired = '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;SameSite=Lax';
        document.cookie = name + expired;
        const host = window.location.hostname;
        if (host) {
            document.cookie = name + expired + ';domain=' + host;
            if (host.indexOf('.') !== -1) {
                document.cookie = name + expired + ';domain=.' + host.replace(/^www\./, '');
            }
        }
    }

    function clearCategoryCookies(category) {
        const patterns = (config.cleanupCookies && config.cleanupCookies[category]) || [];
        if (! patterns.length) return;

        const existing = document.cookie ? document.cookie.split(';') : [];
        const names = {};

        existing.forEach(function (part) {
            const name = part.split('=')[0].trim();
            if (name) names[name] = true;
        });

        Object.keys(names).forEach(function (name) {
            patterns.forEach(function (pattern) {
                if (cookieMatches(pattern, name)) {
                    expireCookie(name);
                }
            });
        });

        // Also try exact configured names even if not present yet (harmless).
        patterns.forEach(function (pattern) {
            if (pattern.slice(-1) !== '*') {
                expireCookie(pattern);
            }
        });
    }

    // Unlock gated nodes marked with data-vcookiebar / data-vcookiebar-category
    // (plain script stubs or template wrappers — never put a closing script tag in this file's comments).
    function activateCategory(category) {
        if (! category) return;

        const nodes = document.querySelectorAll(
            '[data-vcookiebar="' + category + '"],[data-vcookiebar-category="' + category + '"]'
        );

        nodes.forEach(function (node) {
            if (node.getAttribute('data-vcookiebar-activated') === '1') {
                return;
            }

            if (node.tagName === 'TEMPLATE') {
                const content = node.content.cloneNode(true);
                node.parentNode.insertBefore(content, node.nextSibling);
                node.setAttribute('data-vcookiebar-activated', '1');
                return;
            }

            if (node.tagName === 'SCRIPT') {
                const script = document.createElement('script');
                Array.from(node.attributes).forEach(function (attr) {
                    if (attr.name === 'type') return;
                    if (attr.name === 'data-vcookiebar' || attr.name === 'data-vcookiebar-category') return;
                    script.setAttribute(attr.name, attr.value);
                });
                if (node.src) {
                    script.src = node.src;
                } else {
                    script.text = node.textContent || '';
                }
                script.type = 'text/javascript';
                node.parentNode.insertBefore(script, node.nextSibling);
                node.setAttribute('data-vcookiebar-activated', '1');
                node.type = 'application/json';
                return;
            }

            // Generic: reveal hidden nodes
            if (node.hasAttribute('hidden')) {
                node.hidden = false;
            }
            node.setAttribute('data-vcookiebar-activated', '1');
        });
    }

    function applyPreferences(preferences) {
        if (! preferences || typeof preferences !== 'object') {
            return;
        }

        window.__vcookiebar.preferences = preferences;
        Object.keys(preferences).forEach(function (key) {
            if (preferences[key] === true) {
                activateCategory(key);
            } else if (key !== 'necessary') {
                clearCategoryCookies(key);
            }
        });
    }

    function publishConsent(preferences) {
        applyPreferences(preferences);
        window.dispatchEvent(new CustomEvent('vcookiebar:consent', {
            detail: { preferences: preferences },
        }));
    }

    function syncCustomizeUi() {
        const customizeBtn = root ? root.querySelector('[data-vcookiebar-customize]') : null;
        if (! prefsForm || ! customizeBtn) {
            return;
        }

        const expanded = ! prefsForm.hidden;
        customizeBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        const label = expanded
            ? (customizeBtn.getAttribute('data-label-expanded') || config.copy.hideDetails || '')
            : (customizeBtn.getAttribute('data-label-collapsed') || config.copy.customize || '');
        if (label) {
            customizeBtn.textContent = label;
        }
    }

    function setPrefsOpen(open) {
        if (! prefsForm) {
            return;
        }
        prefsForm.hidden = ! open;
        if (open) {
            prefsForm.removeAttribute('hidden');
        } else {
            prefsForm.setAttribute('hidden', 'hidden');
        }
        syncCustomizeUi();
    }

    function hideBanner() {
        if (root) root.hidden = true;
        setPrefsOpen(false);
        if (reopenBtn) {
            reopenBtn.hidden = false;
            reopenBtn.removeAttribute('hidden');
        }
    }

    function showBanner() {
        if (root) {
            root.hidden = false;
            // Collapsed until Customize — keeps label in sync with panel state.
            setPrefsOpen(false);
            syncFormFromPreferences(window.__vcookiebar.preferences || config.preferences);
        }
        if (reopenBtn) {
            reopenBtn.hidden = true;
            reopenBtn.setAttribute('hidden', 'hidden');
        }
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
            if (! response.ok) throw new Error('consent_failed');
            return response.json();
        }).then(function (payload) {
            const saved = payload && payload.preferences ? payload.preferences : preferences;
            // Persist + notify, then reload so revoked scripts unload and
            // gated tags re-evaluate from the consent cookie (GDPR withdrawal).
            publishConsent(saved);
            hideBanner();
            window.location.reload();
        }).catch(function () {
            showError(config.copy.error);
            setBusy(false);
        });
    }

    root?.querySelector('[data-vcookiebar-accept]')?.addEventListener('click', function () {
        submitPreferences(acceptAll());
    });
    root?.querySelector('[data-vcookiebar-reject]')?.addEventListener('click', function () {
        submitPreferences(rejectOptional());
    });
    root?.querySelector('[data-vcookiebar-customize]')?.addEventListener('click', function () {
        if (! prefsForm) return;
        setPrefsOpen(prefsForm.hidden);
    });
    prefsForm?.addEventListener('submit', function (event) {
        event.preventDefault();
        submitPreferences(readPreferencesFromForm());
    });
    reopenBtn?.addEventListener('click', function () {
        showBanner();
    });

    // Existing consent from cookie / host: unlock gated tags immediately.
    if (window.__vcookiebar.preferences) {
        applyPreferences(window.__vcookiebar.preferences);
    }

    // Keep reopen control visible whenever the dialog is closed.
    if (root && root.hidden && reopenBtn) {
        reopenBtn.hidden = false;
        reopenBtn.removeAttribute('hidden');
    }

    // Prefs start collapsed; label matches ("Customize" until opened).
    setPrefsOpen(false);

    window.addEventListener('vcookiebar:consent', function (event) {
        const detail = event?.detail?.preferences;
        if (detail && typeof detail === 'object') {
            applyPreferences(detail);
        }
    });

    // Public API for hosts / page builders
    window.__vcookiebar.has = function (category) {
        const prefs = window.__vcookiebar.preferences;
        return Boolean(prefs && prefs[category] === true);
    };
    window.__vcookiebar.open = showBanner;
})();
</script>
