<?php

declare(strict_types=1);

namespace Tests\Unit\Provider;

use Maduser\Argon\Container\ArgonContainer;
use Maduser\Argon\Container\Compiler\ContainerCompiler;
use Maduser\Argon\Container\Contracts\ServiceDescriptorInterface;
use Maduser\Argon\Http\Message\Factory\ServerRequestFactory;
use Maduser\Argon\Http\Message\Provider\HttpMessageServiceProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HttpMessageServiceProviderTest extends TestCase
{
    public function testServerRequestBindingUsesConcreteFactoryForCurrentRequest(): void
    {
        $container = new ArgonContainer();

        (new HttpMessageServiceProvider())->register($container);

        $factory = $this->getDescriptor($container, ServerRequestFactory::class);
        self::assertSame(ServerRequestFactory::class, $factory->getConcrete());

        $factoryInterface = $this->getDescriptor($container, ServerRequestFactoryInterface::class);
        self::assertSame(ServerRequestFactory::class, $factoryInterface->getConcrete());

        $request = $this->getDescriptor($container, ServerRequestInterface::class);
        self::assertSame(ServerRequestFactory::class, $request->getFactoryClass());
        self::assertSame('__invoke', $request->getFactoryMethod());
    }

    public function testProviderBindingsCanBeCompiled(): void
    {
        $container = new ArgonContainer();

        (new HttpMessageServiceProvider())->register($container);

        $file = sys_get_temp_dir() . '/argon-http-message-provider-' . bin2hex(random_bytes(6)) . '.php';

        try {
            (new ContainerCompiler($container))->compile($file, 'CompiledHttpMessageContainer', 'Tests\\Generated');

            self::assertFileExists($file);
        } finally {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function getDescriptor(ArgonContainer $container, string $id): ServiceDescriptorInterface
    {
        $descriptor = $container->getDescriptor($id);

        self::assertNotNull($descriptor, sprintf('Expected service descriptor for [%s] to be registered.', $id));

        return $descriptor;
    }
}
