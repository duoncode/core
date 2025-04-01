<?php

declare(strict_types=1);

namespace Duon\Core\Exception;

use Exception;
use Duon\Core\Request;
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

	public function getPrettyTrace(): string
	{
		$result = "";
		$traceNumber = 0;

		foreach ($this->getTrace() as $frame) {
			$args = "";

			if (isset($frame['args'])) {
				$args = [];

				foreach ($frame['args'] as $arg) {
					if (is_string($arg)) {
						$args[] = "'" . $arg . "'";
					} elseif (is_array($arg)) {
						$args[] = "Array";
					} elseif (is_null($arg)) {
						$args[] = 'NULL';
					} elseif (is_bool($arg)) {
						$args[] = ($arg) ? "true" : "false";
					} elseif (is_object($arg)) {
						$args[] = get_class($arg);
					} elseif (is_resource($arg)) {
						$args[] = get_resource_type($arg);
					} else {
						$args[] = $arg;
					}
				}

				$args = join(", ", $args);
			}

			$result .= sprintf(
				"<p class=\"trace\"><span class=\"trace-number\">#%s</span>" .
				"<span class=\"trace-file\">%s <span class=\"trace-line-number\">(%s)</span></span>" .
				"<code class=\"trace-code\">%s%s%s(%s)</code></p>\n",
				$traceNumber,
				$frame['file'] ?? '',
				$frame['line'] ?? '',
				isset($frame['class']) ? $frame['class'] : '',
				isset($frame['type']) ? $frame['type'] : '', // "->" or "::"
				$frame['function'] ?? '',
				$args,
			);

			$traceNumber++;
		}

		return $result;
	}
}