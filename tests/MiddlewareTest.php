<?php

declare(strict_types=1);

namespace Duon\Core\Tests;

use Duon\Core\Middleware;
use Duon\Core\Request;
use Duon\Core\Response;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MiddlewareTest extends TestCase
{
	public function testMiddleware(): void
	{
		$factory = new Psr17Factory();
		$creator = new ServerRequestCreator(
			$factory, // ServerRequestFactory
			$factory, // UriFactory
			$factory, // UploadedFileFactory
			$factory,  // StreamFactory
		);
		$request = $creator->fromGlobals();
		$handler = new class implements RequestHandlerInterface {
			public function handle(ServerRequestInterface $request): ResponseInterface
			{
				$factory = new Psr17Factory();

				return $factory->createResponse()->withBody(
					$factory->createStream('test:' . $request->getAttribute('test')),
				);
			}
		};
		$middleware = new class extends Middleware {
			public function handle(Request $request, callable $next): Response
			{
				$request->set('test', 'value');

				$response = $next($request);
				$body = $response->getBody();
				$content = $body->getContents();
				$body->rewind();
				$body->write($content . ':after');
				$response->body($body);

				return $response;
			}
		};
		$response = $middleware->process($request, $handler);

		$this->assertSame('test:value:after', (string) $response->getBody());
	}
}