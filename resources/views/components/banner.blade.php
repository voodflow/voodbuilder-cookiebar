@php
    use Voodflow\Vcookiebar\Support\Banner;
    $enabled = Banner::shouldRenderRuntime();
@endphp

@if ($enabled)
@php
    $config = Banner::runtimeConfig();
    $appearance = $config['appearance'];
    $placement = $appearance['placement'] ?? 'bottom';
    $theme = $appearance['theme'] ?? 'base';
    $showBanner = Banner::shouldRender();
    $cssVars = '';
    foreach ($config['cssVars'] as $name => $value) {
        $cssVars .= $name.':'.$value.';';
    }
@endphp

<div
    id="vcookiebar-shell"
    class="vcookiebar-shell vcookiebar-shell--{{ $placement }} vcookiebar-shell--theme-{{ $theme }}"
    data-vcookiebar-shell
    data-vcookiebar-placement="{{ $placement }}"
    data-vcookiebar-theme="{{ $theme }}"
    style="{{ $cssVars }}"
>
    <aside
        id="vcookiebar-root"
        class="vcookiebar"
        role="dialog"
        aria-modal="false"
        aria-labelledby="vcookiebar-title"
        aria-describedby="vcookiebar-message"
        data-vcookiebar
        @if (! $showBanner) hidden @endif
    >
        <div class="vcookiebar__panel">
            <div class="vcookiebar__copy">
                <h2 id="vcookiebar-title" class="vcookiebar__title">{{ $config['copy']['title'] }}</h2>
                <p id="vcookiebar-message" class="vcookiebar__message">{{ $config['copy']['message'] }}</p>
                @if ($config['privacyPolicyUrl'] || $config['cookiePolicyUrl'])
                    <p class="vcookiebar__privacy">
                        @if ($config['privacyPolicyUrl'])
                            <a
                                href="{{ $config['privacyPolicyUrl'] }}"
                                rel="noopener noreferrer"
                                @if (! empty($config['privacyPolicyNewTab'])) target="_blank" @endif
                            >
                                {{ $config['copy']['privacy'] }}
                            </a>
                        @endif
                        @if ($config['privacyPolicyUrl'] && $config['cookiePolicyUrl'])
                            <span aria-hidden="true"> · </span>
                        @endif
                        @if ($config['cookiePolicyUrl'])
                            <a
                                href="{{ $config['cookiePolicyUrl'] }}"
                                rel="noopener noreferrer"
                                @if (! empty($config['cookiePolicyNewTab'])) target="_blank" @endif
                            >
                                {{ $config['copy']['cookiePolicy'] }}
                            </a>
                        @endif
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
                <button
                    type="button"
                    class="vcookiebar__btn vcookiebar__btn--ghost"
                    data-vcookiebar-customize
                    data-label-collapsed="{{ $config['copy']['customize'] }}"
                    data-label-expanded="{{ $config['copy']['hideDetails'] }}"
                    aria-expanded="false"
                    aria-controls="vcookiebar-prefs"
                >
                    {{ $config['copy']['customize'] }}
                </button>
            </div>

            <form id="vcookiebar-prefs" class="vcookiebar__prefs" data-vcookiebar-prefs hidden>
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

    @if ($appearance['reopen_icon'] ?? true)
        <button
            type="button"
            class="vcookiebar__reopen"
            data-vcookiebar-reopen
            aria-label="{{ $config['copy']['reopen'] }}"
            title="{{ $config['copy']['reopen'] }}"
            @if ($showBanner) hidden @endif
        >
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="currentColor">
                <path d="M12 1a9.5 9.5 0 0 0-6.7 16.2L3 22l4.9-1.3A9.5 9.5 0 1 0 12 1Zm0 2a7.5 7.5 0 1 1 0 15 7.4 7.4 0 0 1-3.5-.9l-.3-.2-2.9.8.8-2.8-.2-.3A7.5 7.5 0 0 1 12 3Z"/>
            </svg>
        </button>
    @endif
</div>

@include('vcookiebar::partials.styles')
@include('vcookiebar::partials.runtime-script', ['config' => $config, 'showBanner' => $showBanner])
@endif
