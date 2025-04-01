<?php

declare(strict_types=1);

namespace Duon\Core\Tests;

use Duon\Core\Exception\HttpBadRequest;
use Duon\Core\Exception\HttpConflict;
use Duon\Core\Exception\HttpForbidden;
use Duon\Core\Exception\HttpGone;
use Duon\Core\Exception\HttpMethodNotAllowed;
use Duon\Core\Exception\HttpNotFound;
use Duon\Core\Exception\HttpUnauthorized;

final class ExceptionTest extends TestCase
{
	public function testHttpBadRequestException(): void
	{
		$request = $this->request();
		$exception = new HttpBadRequest($request, '400 payload');

		$this->assertSame($request, $exception->request());
		$this->assertSame(400, $exception->statusCode());
		$this->assertSame('400 payload', $exception->payload());
		$this->assertSame('400 Bad Request', $exception->title());

		$exception = new HttpBadRequest($request, '400 payload', 'Other Message', null, 666);

		$this->assertSame($request, $exception->request());
		$this->assertSame(666, $exception->statusCode());
		$this->assertSame('400 payload', $exception->payload());
		$this->assertSame('666 Other Message', $exception->title());
	}

	public function testHttpGoneException(): void
	{
		$request = $this->request();
		$exception = new HttpGone($request, '410 payload');

		$this->assertSame($request, $exception->request());
		$this->assertSame(410, $exception->statusCode());
		$this->assertSame('410 payload', $exception->payload());
		$this->assertSame('410 Gone', $exception->title());

		$exception = new HttpGone($request, '410 payload', 'Other Message', null, 666);

		$this->assertSame($request, $exception->request());
		$this->assertSame(666, $exception->statusCode());
		$this->assertSame('410 payload', $exception->payload());
		$this->assertSame('666 Other Message', $exception->title());
	}

	public function testHttpForbiddenException(): void
	{
		$request = $this->request();
		$exception = new HttpForbidden($request, '403 payload');

		$this->assertSame($request, $exception->request());
		$this->assertSame(403, $exception->statusCode());
		$this->assertSame('403 payload', $exception->payload());
		$this->assertSame('403 Forbidden', $exception->title());

		$exception = new HttpForbidden($request, '403 payload', 'Other Message', null, 666);

		$this->assertSame($request, $exception->request());
		$this->assertSame(666, $exception->statusCode());
		$this->assertSame('403 payload', $exception->payload());
		$this->assertSame('666 Other Message', $exception->title());
	}

	public function testHttpConflictException(): void
	{
		$request = $this->request();
		$exception = new HttpConflict($request, '409 payload');

		$this->assertSame($request, $exception->request());
		$this->assertSame(409, $exception->statusCode());
		$this->assertSame('409 payload', $exception->payload());
		$this->assertSame('409 Conflict', $exception->title());

		$exception = new HttpConflict($request, '409 payload', 'Other Message', null, 666);

		$this->assertSame($request, $exception->request());
		$this->assertSame(666, $exception->statusCode());
		$this->assertSame('409 payload', $exception->payload());
		$this->assertSame('666 Other Message', $exception->title());
	}

	public function testHttpMethodNotAllowedException(): void
	{
		$request = $this->request();
		$exception = new HttpMethodNotAllowed($request, '405 payload');

		$this->assertSame($request, $exception->request());
		$this->assertSame(405, $exception->statusCode());
		$this->assertSame('405 payload', $exception->payload());
		$this->assertSame('405 Method Not Allowed', $exception->title());

		$exception = new HttpMethodNotAllowed($request, '405 payload', 'Other Message', null, 666);

		$this->assertSame($request, $exception->request());
		$this->assertSame(666, $exception->statusCode());
		$this->assertSame('405 payload', $exception->payload());
		$this->assertSame('666 Other Message', $exception->title());
	}

	public function testHttpNotFoundException(): void
	{
		$request = $this->request();
		$exception = new HttpNotFound($request, '404 payload');

		$this->assertSame($request, $exception->request());
		$this->assertSame(404, $exception->statusCode());
		$this->assertSame('404 payload', $exception->payload());
		$this->assertSame('404 Not Found', $exception->title());

		$exception = new HttpNotFound($request, '404 payload', 'Other Message', null, 666);

		$this->assertSame($request, $exception->request());
		$this->assertSame(666, $exception->statusCode());
		$this->assertSame('404 payload', $exception->payload());
		$this->assertSame('666 Other Message', $exception->title());
	}

	public function testHttpUnauthorizedException(): void
	{
		$request = $this->request();
		$exception = new HttpUnauthorized($request, '401 payload');

		$this->assertSame($request, $exception->request());
		$this->assertSame(401, $exception->statusCode());
		$this->assertSame('401 payload', $exception->payload());
		$this->assertSame('401 Unauthorized', $exception->title());

		$exception = new HttpUnauthorized($request, '401 payload', 'Other Message', null, 666);

		$this->assertSame($request, $exception->request());
		$this->assertSame(666, $exception->statusCode());
		$this->assertSame('401 payload', $exception->payload());
		$this->assertSame('666 Other Message', $exception->title());
	}
}