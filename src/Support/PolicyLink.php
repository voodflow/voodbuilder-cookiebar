<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

/**
 * Resolve privacy / cookie policy links without hard-requiring Voodbuilder.
 *
 * With Voodbuilder: reuses ResolvableLinkForm field keys (page | url | route).
 * Standalone: supports url | path (site-relative).
 */
final class PolicyLink
{
    public const TYPES_STANDALONE = ['url', 'path'];

    /**
     * @param  array<string, mixed>  $settings
     */
    public static function resolve(array $settings, string $prefix, ?string $legacyUrlKey = null): ?string
    {
        if (class_exists(\Voodflow\Voodbuilder\Support\ResolvableLinkSupport::class, false)) {
            $url = \Voodflow\Voodbuilder\Support\ResolvableLinkSupport::resolve(
                $settings,
                $prefix,
                $legacyUrlKey ?? $prefix.'_url',
            );

            if (filled($url)) {
                return self::normalizeUrl((string) $url);
            }
        }

        $type = strtolower(trim((string) ($settings[$prefix.'_link_type'] ?? '')));
        $target = trim((string) ($settings[$prefix.'_link'] ?? ''));

        if ($target === '' && $legacyUrlKey !== null) {
            $legacy = trim((string) ($settings[$legacyUrlKey] ?? ''));
            if ($legacy !== '') {
                return self::normalizeUrl($legacy);
            }
        }

        if ($target === '') {
            // BC: privacy_policy_url flat string
            if ($prefix === 'privacy' && filled($settings['privacy_policy_url'] ?? null)) {
                return self::normalizeUrl((string) $settings['privacy_policy_url']);
            }

            return null;
        }

        return match ($type) {
            'path' => self::normalizeUrl(str_starts_with($target, '/') ? $target : '/'.$target),
            'page' => self::normalizeUrl('/'.ltrim($target, '/')),
            default => self::normalizeUrl($target),
        };
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
     * Sanitize link fields from admin form before cache.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function extract(array $data, string $prefix): array
    {
        $type = strtolower(trim((string) ($data[$prefix.'_link_type'] ?? '')));
        $link = trim((string) ($data[$prefix.'_link'] ?? ''));

        $allowed = ['url', 'path', 'page', 'route', 'mail'];
        if (! in_array($type, $allowed, true)) {
            $type = 'url';
        }

        $out = [
            $prefix.'_link_type' => $type,
            $prefix.'_link' => $link !== '' ? mb_substr($link, 0, 2048) : null,
        ];

        // Preserve route params when present (Voodbuilder ResolvableLinkForm).
        $routeParams = $data[$prefix.'_route_parameters'] ?? null;
        if (is_array($routeParams)) {
            $out[$prefix.'_route_parameters'] = $routeParams;
        }

        foreach ($data as $key => $value) {
            if (! is_string($key) || ! str_starts_with($key, $prefix.'_route_param_')) {
                continue;
            }
            if (filled($value)) {
                $out[$key] = is_string($value) ? mb_substr($value, 0, 191) : $value;
            }
        }

        return $out;
    }
}
