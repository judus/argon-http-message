<?php

declare(strict_types=1);

namespace Maduser\Argon\Http\Provider;

use Maduser\Argon\Container\AbstractServiceProvider;
use Maduser\Argon\Container\ArgonContainer;
use Maduser\Argon\Container\Contracts\ParameterStoreInterface;
use Maduser\Argon\Container\Exceptions\ContainerException;
use Maduser\Argon\Container\Exceptions\NotFoundException;
use Maduser\Argon\Contracts\Handler\AppHandlerInterface;
use Maduser\Argon\Error\Contracts\ResponseEmitterInterface;
use Maduser\Argon\Http\Kernel;
use Maduser\Argon\Http\ResponseEmitter;
use Override;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @psalm-suppress UnusedClass
 */
final class ArgonHttpFoundation extends AbstractServiceProvider
{
    public function configureParameters(ArgonContainer $container): ParameterStoreInterface
    {
        $parameters = $container->getParameters();

        if (!$parameters->has('kernel.debug')) {
            $debug = isset($_ENV['APP_DEBUG']) && strtolower((string) $_ENV['APP_DEBUG']) === 'true';
            $parameters->set('kernel.debug', $debug);
        }

        if (!$parameters->has('kernel.shouldExit')) {
            $env = strtolower(($_ENV['APP_ENV'] ?? 'production'));
            $shouldExit = $env !== 'testing';
            $parameters->set('kernel.shouldExit', $shouldExit);
        }

        return $parameters;
    }

    /**
     * @throws ContainerException
     * @throws NotFoundException
     */
    #[Override]
    public function register(ArgonContainer $container): void
    {
        $parameters = $this->configureParameters($container);

        /** Logging */
        if (!$container->has(LoggerInterface::class)) {
            $container->set(LoggerInterface::class, NullLogger::class);
        }

        /** PSR-17/7: HTTP Messages */
        $container->register(ArgonMessageServiceProvider::class);
        /** Kernel */
        $container->set(ResponseEmitterInterface::class, ResponseEmitter::class);

        $container->set(AppHandlerInterface::class, Kernel::class, [
            'logger' => LoggerInterface::class,
            'debug' => $parameters->get('debug', false),
            'shouldExit' => $parameters->get('kernel.shouldExit', true),
        ]);
    }

    /**
     */
    #[Override]
    public function boot(ArgonContainer $container): void
    {
        //$container->get(ErrorHandlerInterface::class)->register();
    }
}
