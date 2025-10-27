<?php

declare(strict_types=1);

namespace Tests\Unit;

use Maduser\Argon\Http\Message\ServerRequest;
use Maduser\Argon\Http\Message\Uri;
use PHPUnit\Framework\TestCase;

final class ServerRequestTest extends TestCase
{
    public function testWithUriOverridesHostHeaderWhenNotPreserving(): void
    {
        $request = (new ServerRequest('GET', new Uri('https://example.com')))
            ->withHeader('Host', 'example.com');

        $updated = $request->withUri(new Uri('https://example.org:8443/foo'), false);

        self::assertSame(['example.org:8443'], $updated->getHeader('Host'));
    }

    public function testWithUriKeepsExistingHostWhenPreserving(): void
    {
        $request = (new ServerRequest('GET', new Uri('https://example.com')))
            ->withHeader('Host', 'example.com');

        $updated = $request->withUri(new Uri('https://example.org'), true);

        self::assertSame(['example.com'], $updated->getHeader('Host'));
    }

    public function testWithUriAddsHostWhenMissingAndPreserving(): void
    {
        $request = new ServerRequest('GET', new Uri('https://example.com/path'));

        $updated = $request->withUri(new Uri('https://example.org:9443'), true);

        self::assertSame(['example.org:9443'], $updated->getHeader('Host'));
    }
}
