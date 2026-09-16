<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

use Illuminate\Support\Str;
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
    public static function encode(array $preferences, ?string $visitorId = null): string
    {
        $json = json_encode([
            'v' => 2,
            'vid' => $visitorId ?: (string) Str::uuid(),
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
        $decoded = self::decodeDetailed($payload);

        return $decoded['preferences'] ?? null;
    }

    /**
     * @return array{preferences: array<string, bool>, visitor_id: string, ts: int|null}|null
     */
    public static function decodeDetailed(?string $payload): ?array
    {
        if ($payload === null || $payload === '') {
            return null;
        }

        try {
            $json = base64_decode($payload, strict: true);

            if ($json === false) {
                return null;
            }

            /** @var array{v?: mixed, vid?: mixed, preferences?: mixed, ts?: mixed}|null $data */
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        if (! is_array($data) || ! isset($data['preferences']) || ! is_array($data['preferences'])) {
            return null;
        }

        /** @var array<string, mixed> $preferences */
        $preferences = $data['preferences'];
        $visitorId = is_string($data['vid'] ?? null) && $data['vid'] !== ''
            ? $data['vid']
            : (string) Str::uuid();

        return [
            'preferences' => self::normalize($preferences),
            'visitor_id' => $visitorId,
            'ts' => is_numeric($data['ts'] ?? null) ? (int) $data['ts'] : null,
        ];
    }
}
