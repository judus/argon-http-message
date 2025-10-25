<?php

declare(strict_types=1);

namespace Maduser\Argon\Http\Contracts\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

interface DispatcherInterface extends MiddlewareInterface
{
    public function dispatch(ServerRequestInterface $request): void;
}
