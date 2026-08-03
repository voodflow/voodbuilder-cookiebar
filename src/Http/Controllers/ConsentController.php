<?php

declare(strict_types=1);

namespace Voodflow\Vookiebar\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;
use Voodflow\Vookiebar\Support\ConsentPayload;
use Voodflow\Vookiebar\Vookiebar;

/**
 * Stores visitor cookie-preference choices.
 *
 * Accepts only known category keys; necessary is always forced on.
 */
final class ConsentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (! Vookiebar::isEnabled()) {
            abort(404);
        }

        $allowed = Vookiebar::allowedCategories();

        $validated = $request->validate([
            'preferences' => ['required', 'array'],
            'preferences.*' => ['boolean'],
        ]);

        /** @var array<string, mixed> $preferences */
        $preferences = $validated['preferences'];

        $unknown = array_diff(array_keys($preferences), $allowed);

        if ($unknown !== []) {
            throw ValidationException::withMessages([
                'preferences' => __('vookiebar::runtime.consent.unknown_categories'),
            ]);
        }

        foreach ($allowed as $category) {
            if (! array_key_exists($category, $preferences)) {
                continue;
            }

            // Reject non-boolean after casting edge cases from JSON.
            if (! is_bool($preferences[$category])) {
                throw ValidationException::withMessages([
                    "preferences.{$category}" => __('vookiebar::runtime.consent.invalid_preference'),
                ]);
            }
        }

        $normalized = ConsentPayload::normalize($preferences);

        $cookieName = (string) config('vookiebar.consent_cookie', 'vookiebar_consent');
        $lifetime = max(1, (int) config('vookiebar.consent_lifetime_minutes', 60 * 24 * 365));

        $payload = ConsentPayload::encode($normalized);

        Cookie::queue(
            cookie(
                $cookieName,
                $payload,
                $lifetime,
                httpOnly: true,
                secure: $request->secure(),
                sameSite: 'Lax',
            ),
        );

        return response()->json([
            'ok' => true,
            'preferences' => $normalized,
        ]);
    }
}
