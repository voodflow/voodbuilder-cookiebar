<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Events;

/**
 * Fired after a visitor consent cookie is queued.
 *
 * Preferences contain no PII. Optional anonymous visitor_id lets companions
 * store per-visitor evidence without changing Voodflow core.
 *
 * @param  array<string, bool>  $preferences
 */
final class VisitorConsentSaved
{
    /**
     * @param  array<string, bool>  $preferences
     */
    public function __construct(
        public readonly array $preferences,
        public readonly string $visitorId,
        public readonly ?string $ipHash = null,
        public readonly ?string $userAgentHash = null,
    ) {}
}
