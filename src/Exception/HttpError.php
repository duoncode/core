<?php

declare(strict_types=1);

namespace FiveOrbs\Core\Exception;

use Exception;
use FiveOrbs\Core\Request;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequest;
use Throwable;

/**
 * @psalm-api
 *
 * @psalm-consistent-constructor
 */
abstract class HttpError extends Exception implements CoreException
{
	/** @var int<0,599> */
	protected const int code = 0;

	/** @var string */
	protected const string message = '';

	protected ?PsrServerRequest $request;

	public function __construct(
		Request|PsrServerRequest|null $request = null,
		protected mixed $payload = null,
		string $message = '',
		?Throwable $previous = null,
		int $code = 0,
	) {
		parent::__construct($message ?: static::message, $code ?: static::code, $previous);

		$this->request = $request instanceof Request ? $request->unwrap() : $request;
	}

	public function title(): string
	{
		return (string) $this->getCode() . ' ' . $this->getMessage();
	}

	public function payload(): mixed
	{
		return $this->payload;
	}

	public function request(): ?PsrServerRequest
	{
		return $this->request;
	}

	public function statusCode(): int
	{
		return $this->getCode();
	}
}
