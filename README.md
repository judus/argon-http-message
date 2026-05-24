# Argon HTTP Message

[![PHP](https://img.shields.io/badge/php-8.2+-blue)](https://www.php.net/)
[![Build](https://github.com/judus/argon-http-message/actions/workflows/php.yml/badge.svg)](https://github.com/judus/argon-http-message/actions)
[![codecov](https://codecov.io/gh/judus/argon-http-message/branch/master/graph/badge.svg)](https://codecov.io/gh/judus/argon-http-message)
[![Psalm Level](https://shepherd.dev/github/judus/argon-http-message/coverage.svg)](https://shepherd.dev/github/judus/argon-http-message)
[![Latest Version](https://img.shields.io/packagist/v/maduser/argon-http-message.svg)](https://packagist.org/packages/maduser/argon-http-message)
[![Downloads](https://img.shields.io/packagist/dt/maduser/argon-http-message.svg)](https://packagist.org/packages/maduser/argon-http-message)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

`maduser/argon-http-message` provides the PSR-7 message objects and PSR-17
factories used by the Argon HTTP stack.

## Installation

```bash
composer require maduser/argon-http-message
```

## Service Provider

Register `HttpMessageServiceProvider` in an Argon container:

```php
use Maduser\Argon\Http\Message\Provider\HttpMessageServiceProvider;

$container->register(HttpMessageServiceProvider::class);
```

The provider binds PSR interfaces for server requests, responses, streams, URIs,
and uploaded files, plus the matching PSR-17 factories.

## Scope

This package does not run an HTTP kernel, emit responses, route requests, or
format exceptions. It only provides HTTP messages and factories.

## Quality Gate

```bash
composer check
```
