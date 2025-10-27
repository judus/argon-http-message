<?php

declare(strict_types=1);

namespace Tests\Unit;

use JsonException;
use Maduser\Argon\Http\Message\Response;
use Maduser\Argon\Http\Message\Stream;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testWithHeaderCastsScalarValuesToStrings(): void
    {
        $response = new Response();
        $updated = $response->withHeader('X-Test', 123);

        self::assertSame(['123'], $updated->getHeader('X-Test'));
    }

    public function testWithBodyUpdatesContentLength(): void
    {
        $response = new Response();

        $newBody = new Stream('hello');
        $updated = $response->withBody($newBody);

        self::assertSame(['5'], $updated->getHeader('Content-Length'));
    }

    public function testAppendBodyDoesNotMutateOriginalStream(): void
    {
        $response = new Response(new Stream('hello'));
        $appended = $response->appendBody(' world');

        self::assertSame('hello', (string) $response->getBody());
        self::assertSame('hello world', (string) $appended->getBody());
        self::assertSame(['11'], $appended->getHeader('Content-Length'));
    }

    public function testWithJsonThrowsOnEncodingErrors(): void
    {
        $response = new Response();

        $this->expectException(JsonException::class);

        $response->withJson(['value' => NAN], JSON_PRETTY_PRINT);
    }
}
