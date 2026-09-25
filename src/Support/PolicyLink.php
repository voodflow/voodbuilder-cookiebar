<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

use Illuminate\Support\Str;

/**
 * Resolve privacy / cookie policy links without hard-requiring Voodbuilder.
 *
 * Link types mirror the Voodbuilder button picker when available:
 *   url | page | menu
 * Standalone fallback: url | path
 *
 * Page targets may be a SitePage slug or a translation_group_id. On the public
 * site the URL is resolved for the visitor's current locale when translations exist.
 */
final class PolicyLink
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public static function resolve(array $settings, string $prefix, ?string $legacyUrlKey = null): ?string
    {
        $type = strtolower(trim((string) ($settings[$prefix . '_link_type'] ?? '')));
        $target = trim((string) ($settings[$prefix . '_link'] ?? ''));

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
            'path' => self::normalizeUrl(str_starts_with($target, '/') ? $target : '/' . $target),
            default => self::normalizeUrl($target),
        };
    }

    public static function opensInNewTab(array $settings, string $prefix): bool
    {
        $target = (string) ($settings[$prefix . '_open_in'] ?? '');

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
        $type = strtolower(trim((string) ($data[$prefix . '_link_type'] ?? '')));
        $link = trim((string) ($data[$prefix . '_link'] ?? ''));
        $openIn = (string) ($data[$prefix . '_open_in'] ?? '');

        $allowed = ['url', 'path', 'page', 'menu'];
        if (! in_array($type, $allowed, true)) {
            $type = 'url';
        }

        if ($openIn !== '_blank') {
            $openIn = '';
        }

        return [
            $prefix . '_link_type' => $type,
            $prefix . '_link' => $link !== '' ? mb_substr($link, 0, 2048) : null,
            $prefix . '_open_in' => $openIn,
        ];
    }

    private static function resolvePage(string $target): ?string
    {
        $pageClass = 'Voodflow\\Voodbuilder\\Models\\SitePage';
        if (! class_exists($pageClass)) {
            return self::normalizeUrl('/' . ltrim($target, '/'));
        }

        $page = self::findPage($pageClass, $target);
        if ($page === null) {
            return null;
        }

        $page = self::localizePage($page);

        $url = method_exists($page, 'getUrl') ? (string) $page->getUrl() : '';

        return filled($url) && $url !== '#' ? self::normalizeUrl($url) : null;
    }

    /**
     * @param  class-string  $pageClass
     */
    private static function findPage(string $pageClass, string $target): ?object
    {
        if (Str::isUuid($target)) {
            /** @var object|null $byGroup */
            $byGroup = $pageClass::query()
                ->where('translation_group_id', $target)
                ->orderBy('id')
                ->first();

            if ($byGroup !== null) {
                return $byGroup;
            }
        }

        $locale = self::preferredLocale();
        $query = $pageClass::query()->where('slug', $target);

        if ($locale !== '' && self::pagesAreLocalized()) {
            /** @var object|null $exact */
            $exact = (clone $query)->where('locale', $locale)->first();

            if ($exact !== null) {
                return $exact;
            }
        }

        /** @var object|null $any */
        $any = $query->orderBy('id')->first();

        return $any;
    }

    private static function pagesAreLocalized(): bool
    {
        $resolver = 'Voodflow\\Voodbuilder\\Support\\SitePageResolver';

        return class_exists($resolver)
            && method_exists($resolver, 'hasLocalizationColumns')
            && (bool) $resolver::hasLocalizationColumns();
    }

    private static function localizePage(object $page): object
    {
        $locale = self::preferredLocale();

        if ($locale === '' || ! method_exists($page, 'translationFor')) {
            return $page;
        }

        /** @var object|null $translated */
        $translated = $page->translationFor($locale);

        return $translated ?? $page;
    }

    private static function preferredLocale(): string
    {
        $pageClass = 'Voodflow\\Voodbuilder\\Models\\SitePage';

        if (class_exists($pageClass) && method_exists($pageClass, 'currentLocale')) {
            return (string) $pageClass::currentLocale();
        }

        return (string) app()->getLocale();
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
