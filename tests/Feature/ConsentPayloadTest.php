<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Tests\Feature;

use Voodflow\Vcookiebar\Support\ConsentPayload;
use Voodflow\Vcookiebar\Tests\TestCase;

class ConsentPayloadTest extends TestCase
{
    public function test_decode_rejects_tampered_payload(): void
    {
        $this->assertNull(ConsentPayload::decode('not-base64!!!'));
        $this->assertNull(ConsentPayload::decode(base64_encode('{bad')));
        $this->assertNull(ConsentPayload::decode(base64_encode(json_encode(['v' => 1]))));
    }

    public function test_round_trip_preserves_normalized_preferences(): void
    {
        $encoded = ConsentPayload::encode([
            'necessary' => true,
            'preferences' => true,
            'analytics' => false,
            'marketing' => true,
        ]);

        $decoded = ConsentPayload::decode($encoded);

        $this->assertSame([
            'necessary' => true,
            'preferences' => true,
            'analytics' => false,
            'marketing' => true,
        ], $decoded);
    }
}
