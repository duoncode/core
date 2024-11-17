<?php

declare(strict_types=1);

namespace FiveOrbs\Core\Factory;

use FiveOrbs\Core\Exception\RuntimeException;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/** @psalm-api */
class Guzzle extends AbstractFactory
{
	public function __construct()
	{
		try {
			$factory = new HttpFactory();
			$this->responseFactory = $factory;
			$this->streamFactory = $factory;
			$this->requestFactory = $factory;
			$this->serverRequestFactory = $factory;
			$this->uploadedFileFactory = $factory;
			$this->uriFactory = $factory;
			// @codeCoverageIgnoreStart
		} catch (Throwable) {
			throw new RuntimeException('Install guzzlehttp/psr7');
			// @codeCoverageIgnoreEnd
		}
	}

	public function serverRequest(): ServerRequestInterface
	{
		return ServerRequest::fromGlobals();
	}
}
