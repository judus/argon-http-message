<?php

declare(strict_types=1);

namespace Maduser\Argon\Http\Message\Provider;

use Maduser\Argon\Container\AbstractServiceProvider;
use Maduser\Argon\Container\ArgonContainer;
use Maduser\Argon\Container\Exceptions\ContainerException;
use Maduser\Argon\Http\Message\Factory\ResponseFactory;
use Maduser\Argon\Http\Message\Factory\ServerRequestFactory;
use Maduser\Argon\Http\Message\Factory\StreamFactory;
use Maduser\Argon\Http\Message\Factory\UploadedFileFactory;
use Maduser\Argon\Http\Message\Factory\UriFactory;
use Maduser\Argon\Http\Message\Response;
use Maduser\Argon\Http\Message\ServerRequest;
use Maduser\Argon\Http\Message\Stream;
use Maduser\Argon\Http\Message\UploadedFile;
use Maduser\Argon\Http\Message\Uri;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * @psalm-api
 */
final class HttpMessageServiceProvider extends AbstractServiceProvider
{
    /**
     * @throws ContainerException
     */
    #[Override]
    public function register(ArgonContainer $container): void
    {
        $container->set(ServerRequestFactory::class)
            ->tag(['http', 'psr-17']);

        $container->set(ServerRequestFactoryInterface::class, ServerRequestFactory::class)
            ->tag(['http', 'psr-17']);

        $container->set(ServerRequestInterface::class, ServerRequest::class)
            ->factory(ServerRequestFactory::class)
            ->tag(['http', 'psr-7'])
            ->transient();

        $container->set(ResponseFactoryInterface::class, ResponseFactory::class)
            ->tag(['http', 'psr-17']);

        $container->set(ResponseInterface::class, Response::class)
            ->factory(ResponseFactoryInterface::class, 'createResponse')
            ->tag(['http', 'psr-7'])
            ->transient();

        $container->set(StreamFactoryInterface::class, StreamFactory::class)
            ->tag(['http', 'psr-17']);

        $container->set(StreamInterface::class, Stream::class)
            ->factory(StreamFactoryInterface::class, 'createStream')
            ->tag(['http', 'psr-7'])
            ->transient();

        $container->set(UriFactoryInterface::class, UriFactory::class)
            ->tag(['http', 'psr-17']);

        $container->set(UriInterface::class, Uri::class)
            ->factory(UriFactoryInterface::class, 'createUri')
            ->tag(['http', 'psr-7'])
            ->transient();

        $container->set(UploadedFileFactoryInterface::class, UploadedFileFactory::class)
            ->tag(['http', 'psr-17']);

        $container->set(UploadedFileInterface::class, UploadedFile::class)
            ->factory(UploadedFileFactoryInterface::class, 'createUploadedFile')
            ->tag(['http', 'psr-7'])
            ->transient();
    }
}
