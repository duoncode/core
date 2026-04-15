<?php

declare(strict_types=1);

namespace Duon\Core;

use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @psalm-api */
abstract class Middleware implements MiddlewareInterface
{
	#[Override]
	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler,
	): ResponseInterface {
		return $this->handle(
			new Request($request),
			static function (Request $request) use ($handler): Response {
				return new Response($handler->handle($request->unwrap()));
			},
		)->unwrap();
	}

	/**
	 * @param callable(Request): Response $next
	 */
	abstract public function handle(Request $request, callable $next): Response;
}
