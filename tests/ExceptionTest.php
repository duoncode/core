<?php

declare(strict_types=1);

namespace Conia\Core\Tests;

use Conia\Core\Exception\HttpBadRequest;
use Conia\Core\Exception\HttpForbidden;
use Conia\Core\Exception\HttpMethodNotAllowed;
use Conia\Core\Exception\HttpNotFound;
use Conia\Core\Exception\HttpUnauthorized;

final class ExceptionTest extends TestCase
{
    public function testHttpExceptions(): void
    {
        $exception = HttpBadRequest::withPayload('400 payload');
        $this->assertSame('400 Bad Request', $exception->getTitle());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame('400 payload', $exception->getPayload());

        $exception = HttpUnauthorized::withPayload('401 payload');
        $this->assertSame('401 Unauthorized', $exception->getTitle());
        $this->assertSame(401, $exception->getCode());
        $this->assertSame('401 payload', $exception->getPayload());

        $exception = HttpForbidden::withPayload('403 payload');
        $this->assertSame('403 Forbidden', $exception->getTitle());
        $this->assertSame(403, $exception->getCode());
        $this->assertSame('403 payload', $exception->getPayload());

        $exception = HttpNotFound::withPayload('404 payload');
        $this->assertSame('404 Not Found', $exception->getTitle());
        $this->assertSame(404, $exception->getCode());
        $this->assertSame('404 payload', $exception->getPayload());

        $exception = HttpMethodNotAllowed::withPayload('405 payload');
        $this->assertSame('405 Method Not Allowed', $exception->getTitle());
        $this->assertSame(405, $exception->getCode());
        $this->assertSame('405 payload', $exception->getPayload());
    }
}
