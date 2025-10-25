<?php

declare(strict_types=1);

namespace Maduser\Argon\Http\Provider;

use Maduser\Argon\Container\AbstractServiceProvider;
use Maduser\Argon\Container\ArgonContainer;
use Maduser\Argon\Container\Exceptions\ContainerException;
use Maduser\Argon\Contracts\Http\Server\Factory\RequestHandlerFactoryInterface;
use Maduser\Argon\Http\Server\Factory\RequestHandlerFactory;
use Maduser\Argon\Http\Server\MiddlewarePipeline;
use Maduser\Argon\Middleware\Contracts\ResultContextInterface;
use Maduser\Argon\Middleware\ResultContext;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class ArgonRequestHandlerServiceProvider extends AbstractServiceProvider
{
    /**
     * @throws ContainerException
     */
    public function register(ArgonContainer $container): void
    {
        $container->set(RequestHandlerFactoryInterface::class, RequestHandlerFactory::class, [
            'logger' => LoggerInterface::class,
        ])->tag(['request_handler_factory']);

        $container->set(RequestHandlerInterface::class, MiddlewarePipeline::class)
            ->factory(RequestHandlerFactoryInterface::class, 'create')
            ->tag(['middleware.pipeline', 'psr-15']);

        $container->set(ResultContextInterface::class, ResultContext::class);
    }
}
