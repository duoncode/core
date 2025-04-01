<?php

declare(strict_types=1);

namespace Duon\Core\Tests;

use Duon\Core\Exception\ValueError;
use Duon\Core\Factory\Laminas;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

final class FactoryLaminasTest extends TestCase
{
	public function testLaminas(): void
	{
		$factory = new Laminas();

		$serverRequest = $factory->serverRequest();
		$this->assertInstanceOf(\Laminas\Diactoros\ServerRequest::class, $serverRequest);

		$request = $factory->request('GET', 'http://example.com');
		$this->assertInstanceOf(\Laminas\Diactoros\Request::class, $request);

		$response = $factory->response();
		$this->assertInstanceOf(\Laminas\Diactoros\Response::class, $response);

		$response = $factory->response(404, 'changed phrase');
		$this->assertEquals('changed phrase', $response->getReasonPhrase());
		$this->assertEquals(404, $response->getStatusCode());

		$stream = $factory->stream();
		$this->assertInstanceOf(\Laminas\Diactoros\Stream::class, $stream);

		$stream = $factory->streamFromResource(fopen('php://temp', 'r+'));
		$this->assertInstanceOf(\Laminas\Diactoros\Stream::class, $stream);

		$stream = $factory->streamFromFile(__DIR__ . '/Fixtures/public/image.webp');
		$this->assertInstanceOf(\Laminas\Diactoros\Stream::class, $stream);

		$uri = $factory->uri('http://example.com');
		$this->assertInstanceOf(\Laminas\Diactoros\Uri::class, $uri);

		$uploadedFile = $factory->uploadedFile($stream);
		$this->assertInstanceOf(\Laminas\Diactoros\UploadedFile::class, $uploadedFile);

		$this->assertInstanceOf(RequestFactoryInterface::class, $factory->requestFactory());
		$this->assertInstanceOf(ServerRequestFactoryInterface::class, $factory->serverRequestFactory());
		$this->assertInstanceOf(ResponseFactoryInterface::class, $factory->responseFactory());
		$this->assertInstanceOf(StreamFactoryInterface::class, $factory->streamFactory());
		$this->assertInstanceOf(UploadedFileFactoryInterface::class, $factory->uploadedFileFactory());
		$this->assertInstanceOf(UriFactoryInterface::class, $factory->uriFactory());
	}

	public function testLaminasFailingResource(): void
	{
		$this->throws(ValueError::class);

		$factory = new Laminas();
		$factory->streamFromResource('wrong');
	}
}