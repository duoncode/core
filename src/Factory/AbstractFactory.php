<?php

declare(strict_types=1);

namespace Conia\Core\Factory;

use Conia\Core\Exception\ValueError;
use Conia\Core\Factory;
use Psr\Http\Message\RequestFactoryInterface as Requestfactory;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestFactoryInterface as ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Http\Message\StreamInterface as Stream;
use Psr\Http\Message\UploadedFileFactoryInterface as UploadedFileFactory;
use Psr\Http\Message\UploadedFileInterface as UploadedFile;
use Psr\Http\Message\UriFactoryInterface as UriFactory;
use Psr\Http\Message\UriInterface as Uri;
use Stringable;

/** @psalm-api */
abstract class AbstractFactory implements Factory
{
    protected RequestFactory $requestFactory;
    protected ResponseFactory $responseFactory;
    protected ServerRequestFactory $serverRequestFactory;
    protected StreamFactory $streamFactory;
    protected UploadedFileFactory $uploadedFileFactory;
    protected UriFactory $uriFactory;

    abstract public function serverRequest(): ServerRequest;

    public function request(string $method, Uri|string $uri): Request
    {
        return $this->requestFactory->createRequest($method, $uri);
    }

    public function response(int $code = 200, string $reasonPhrase = ''): Response
    {
        if ($reasonPhrase === '') {
            return  $this->responseFactory->createResponse($code);
        }

        return  $this->responseFactory->createResponse($code, $reasonPhrase);
    }

    public function stream(string|Stringable $content = ''): Stream
    {
        return $this->streamFactory->createStream((string)$content);
    }

    public function streamFromFile(string $filename, string $mode = 'r'): Stream
    {
        return $this->streamFactory->createStreamFromFile($filename, $mode);
    }

    public function streamFromResource(mixed $resource): Stream
    {
        if (is_resource($resource)) {
            return $this->streamFactory->createStreamFromResource($resource);
        }

        throw new ValueError('Value must be a valid resource');
    }

    public function uploadedFile(
        Stream $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFile {
        return $this->uploadedFileFactory->createUploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    public function uri(string $uri = ''): Uri
    {
        return $this->uriFactory->createUri($uri);
    }

    public function responseFactory(): ResponseFactory
    {
        return $this->responseFactory;
    }

    public function requestFactory(): RequestFactory
    {
        return $this->requestFactory;
    }

    public function streamFactory(): StreamFactory
    {
        return $this->streamFactory;
    }

    public function serverRequestFactory(): ServerRequestFactory
    {
        return $this->serverRequestFactory;
    }

    public function uploadedFileFactory(): UploadedFileFactory
    {
        return $this->uploadedFileFactory;
    }

    public function uriFactory(): UriFactory
    {
        return $this->uriFactory;
    }
}
