<?php

declare(strict_types=1);

namespace Tests\Unit\Factory;

use Maduser\Argon\Http\Message\Factory\ServerRequestFactory;
use Maduser\Argon\Http\Message\ServerRequest;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class ServerRequestFactoryNativeHeadersTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testUsesNativeGetAllHeadersFunction(): void
    {
        if (!function_exists('getallHeaders')) {
            $definition = <<<'PHP'
namespace {
    function getallHeaders(): array {
        return [
            'X-From-Function' => 'value',
            'Content-Type' => 'application/json',
        ];
    }
}
PHP;
            eval($definition);
        }

        $backupServer = $_SERVER ?? [];
        $backupGet = $_GET ?? [];
        $backupPost = $_POST ?? [];
        $backupFiles = $_FILES ?? [];
        $backupCookie = $_COOKIE ?? [];

        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        ];
        $_GET = $_POST = $_FILES = $_COOKIE = [];

        try {
            $headers = getallHeaders();
            $this->assertSame([
                'X-From-Function' => 'value',
                'Content-Type' => 'application/json',
            ], $headers);

            $request = ServerRequestFactory::fromGlobals();
            $this->assertInstanceOf(ServerRequest::class, $request);
            $this->assertSame(['value'], $request->getHeader('x-from-function'));
            $this->assertSame(['application/json'], $request->getHeader('content-type'));
        } finally {
            $_SERVER = $backupServer;
            $_GET = $backupGet;
            $_POST = $backupPost;
            $_FILES = $backupFiles;
            $_COOKIE = $backupCookie;
        }
    }
}
