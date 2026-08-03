<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

use Voodflow\Vcookiebar\Vcookiebar;

/**
 * Normalizes and encodes visitor consent preferences for the consent cookie.
 */
final class ConsentPayload
{
    /**
     * @param  array<string, mixed>  $preferences
     * @return array<string, bool>
     */
    public static function normalize(array $preferences): array
    {
        $normalized = Vcookiebar::defaultPreferences();

        foreach (Vcookiebar::allowedCategories() as $category) {
            if (! array_key_exists($category, $preferences)) {
                continue;
            }

            $normalized[$category] = (bool) $preferences[$category];
        }

        if (array_key_exists('necessary', $normalized)) {
            $normalized['necessary'] = true;
        }

        return $normalized;
    }

    /**
     * @param  array<string, bool>  $preferences
     */
    public static function encode(array $preferences): string
    {
        $json = json_encode([
            'v' => 1,
            'preferences' => $preferences,
            'ts' => time(),
        ], JSON_THROW_ON_ERROR);

        return base64_encode($json);
    }

    /**
     * @return array<string, bool>|null
     */
    public static function decode(?string $payload): ?array
    {
        if ($payload === null || $payload === '') {
            return null;
        }

        try {
            $json = base64_decode($payload, strict: true);

            if ($json === false) {
                return null;
            }

            /** @var array{v?: mixed, preferences?: mixed}|null $data */
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        if (! is_array($data) || ! isset($data['preferences']) || ! is_array($data['preferences'])) {
            return null;
        }

        /** @var array<string, mixed> $preferences */
        $preferences = $data['preferences'];

        return self::normalize($preferences);
    }
}
