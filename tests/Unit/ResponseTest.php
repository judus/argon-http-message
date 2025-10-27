<?php

declare(strict_types=1);

namespace Tests\Unit;

use JsonException;
use Maduser\Argon\Http\Message\Response;
use Maduser\Argon\Http\Message\Stream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Response::class)]
final class ResponseTest extends TestCase
{
    public function testDefaultConstructorValues(): void
    {
        $response = new Response();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getReasonPhrase());
        $this->assertSame('1.1', $response->getProtocolVersion());
        $this->assertTrue($response->hasHeader('content-length'));
    }

    public function testWithProtocolVersion(): void
    {
        $response = (new Response())->withProtocolVersion('2.0');
        $this->assertSame('2.0', $response->getProtocolVersion());
    }

    public function testHeaderManipulation(): void
    {
        $response = (new Response())
            ->withHeader('X-Test', 'value')
            ->withAddedHeader('X-Test', 'another')
            ->withoutHeader('content-length');

        $this->assertSame(['value', 'another'], $response->getHeader('x-test'));
        $this->assertFalse($response->hasHeader('content-length'));
    }

    public function testWithHeaderCastsScalarValuesToStrings(): void
    {
        $response = (new Response())->withHeader('X-Test', 123);

        $this->assertSame(['123'], $response->getHeader('X-Test'));
    }

    public function testWithHeaderAcceptsArrayValues(): void
    {
        $response = (new Response())->withHeader('X-Test', ['one', 2]);

        $this->assertSame(['one', '2'], $response->getHeader('X-Test'));
    }

    public function testGetHeadersReturnsExpectedArray(): void
    {
        $response = new Response();
        $responseWithHeader = $response->withHeader('X-Test', 'value');

        $headers = $responseWithHeader->getHeaders();

        $this->assertArrayHasKey('x-test', $headers);
        $this->assertEquals(['value'], $headers['x-test']);
    }

    public function testConstructorNormalizesHeaders(): void
    {
        $response = new Response(body: new Stream(''), headers: [
            'X-Scalar' => 42,
            'X-List' => ['foo', 123],
        ]);

        $headers = $response->getHeaders();

        $this->assertSame(['42'], $headers['x-scalar']);
        $this->assertSame(['foo', '123'], $headers['x-list']);
    }

    public function testBodyManipulation(): void
    {
        $stream = Stream::fromString('foobar');
        $response = (new Response())->withBody($stream);

        $this->assertSame('foobar', (string) $response->getBody());
        $this->assertSame(['6'], $response->getHeader('content-length'));

        $appended = $response->appendBody('baz');
        $this->assertStringContainsString('baz', (string) $appended->getBody());
        $this->assertSame(['9'], $appended->getHeader('content-length'));
    }

    public function testAppendBodyDoesNotMutateOriginalStream(): void
    {
        $response = new Response(new Stream('hello'));
        $appended = $response->appendBody(' world');

        $this->assertSame('hello', (string) $response->getBody());
        $this->assertSame('hello world', (string) $appended->getBody());
        $this->assertSame(['11'], $appended->getHeader('Content-Length'));
    }

    public function testWithAddedHeaderAcceptsArrayValues(): void
    {
        $response = new Response();
        $updated = $response->withAddedHeader('X-Test', ['one', 2]);

        $this->assertSame(['one', '2'], $updated->getHeader('X-Test'));
    }

    public function testWithBodyRemovesContentLengthWhenSizeUnknown(): void
    {
        $resource = fopen('php://output', 'w');
        $this->assertNotFalse($resource);

        $response = new Response(new Stream('123456'));
        $nullSizeStream = Stream::fromResource($resource);

        $updated = $response->withBody($nullSizeStream);

        $this->assertFalse($updated->hasHeader('Content-Length'));
        $nullSizeStream->detach();
    }

    public function testWithStatusAndReason(): void
    {
        $response = (new Response())->withStatus(404);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Not Found', $response->getReasonPhrase());
    }

    public function testWithCustomStatusMessage(): void
    {
        $response = (new Response())->withStatusMessage('Custom Reason');

        $this->assertSame('Custom Reason', $response->getReasonPhrase());
    }

    public function testWithJson(): void
    {
        $data = ['key' => 'value'];
        $response = (new Response())->withJson($data);

        $this->assertStringContainsString('application/json', $response->getHeaderLine('content-type'));
        $this->assertJson((string) $response->getBody());
    }

    public function testWithHtml(): void
    {
        $html = '<p>Hello</p>';
        $response = (new Response())->withHtml($html);

        $this->assertStringContainsString('text/html', $response->getHeaderLine('content-type'));
        $this->assertStringContainsString($html, (string) $response->getBody());
    }

    public function testWithText(): void
    {
        $text = 'plain text';
        $response = (new Response())->withText($text);

        $this->assertStringContainsString('text/plain', $response->getHeaderLine('content-type'));
        $this->assertStringContainsString($text, (string) $response->getBody());
    }

    public function testStaticFactoryMethods(): void
    {
        $text = Response::text('hello');
        $this->assertStringContainsString('text/plain', $text->getHeaderLine('content-type'));

        $html = Response::html('<b>html</b>');
        $this->assertStringContainsString('text/html', $html->getHeaderLine('content-type'));

        $json = Response::json(['foo' => 'bar']);
        $this->assertStringContainsString('application/json', $json->getHeaderLine('content-type'));
    }

    public function testWithJsonThrowsOnEncodingErrors(): void
    {
        $response = new Response();

        $this->expectException(JsonException::class);

        $response->withJson(['value' => NAN], JSON_PRETTY_PRINT);
    }
}
