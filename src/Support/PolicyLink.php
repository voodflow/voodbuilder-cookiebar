<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

/**
 * Resolve privacy / cookie policy links without hard-requiring Voodbuilder.
 *
 * Link types mirror the Voodbuilder button picker when available:
 *   url | page | menu
 * Standalone fallback: url | path
 */
final class PolicyLink
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public static function resolve(array $settings, string $prefix, ?string $legacyUrlKey = null): ?string
    {
        $type = strtolower(trim((string) ($settings[$prefix.'_link_type'] ?? '')));
        $target = trim((string) ($settings[$prefix.'_link'] ?? ''));

        if ($target === '' && $legacyUrlKey !== null) {
            $legacy = trim((string) ($settings[$legacyUrlKey] ?? ''));
            if ($legacy !== '') {
                return self::normalizeUrl($legacy);
            }
        }

        if ($target === '') {
            if ($prefix === 'privacy' && filled($settings['privacy_policy_url'] ?? null)) {
                return self::normalizeUrl((string) $settings['privacy_policy_url']);
            }

            return null;
        }

        return match ($type) {
            'page' => self::resolvePage($target),
            'menu' => self::resolveMenuItem($target),
            'path' => self::normalizeUrl(str_starts_with($target, '/') ? $target : '/'.$target),
            default => self::normalizeUrl($target),
        };
    }

    public static function opensInNewTab(array $settings, string $prefix): bool
    {
        $target = (string) ($settings[$prefix.'_open_in'] ?? '');

        return $target === '_blank';
    }

    public static function normalizeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return url($url);
        }

        return $url;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function extract(array $data, string $prefix): array
    {
        $type = strtolower(trim((string) ($data[$prefix.'_link_type'] ?? '')));
        $link = trim((string) ($data[$prefix.'_link'] ?? ''));
        $openIn = (string) ($data[$prefix.'_open_in'] ?? '');

        $allowed = ['url', 'path', 'page', 'menu'];
        if (! in_array($type, $allowed, true)) {
            $type = 'url';
        }

        if ($openIn !== '_blank') {
            $openIn = '';
        }

        return [
            $prefix.'_link_type' => $type,
            $prefix.'_link' => $link !== '' ? mb_substr($link, 0, 2048) : null,
            $prefix.'_open_in' => $openIn,
        ];
    }

    private static function resolvePage(string $slug): ?string
    {
        $pageClass = 'Voodflow\\Voodbuilder\\Models\\SitePage';
        if (! class_exists($pageClass)) {
            return self::normalizeUrl('/'.ltrim($slug, '/'));
        }

        /** @var object|null $page */
        $page = $pageClass::query()->where('slug', $slug)->first();
        if ($page === null) {
            return null;
        }

        $url = method_exists($page, 'getUrl') ? (string) $page->getUrl() : '';

        return filled($url) && $url !== '#' ? self::normalizeUrl($url) : null;
    }

    private static function resolveMenuItem(string $id): ?string
    {
        $itemClass = 'Voodflow\\Voodbuilder\\Models\\NavigationMenuItem';
        if (! class_exists($itemClass)) {
            return null;
        }

        /** @var object|null $item */
        $item = $itemClass::query()->find($id);
        if ($item === null || ! method_exists($item, 'resolveUrl')) {
            return null;
        }

        $url = (string) $item->resolveUrl();

        return filled($url) && $url !== '#' ? self::normalizeUrl($url) : null;
    }
}
