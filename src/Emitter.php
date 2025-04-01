<?php

declare(strict_types=1);

namespace Duon\Core;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Psr\Http\Message\ResponseInterface;

class Emitter implements EmitterInterface
{
	protected EmitterStack $stack;

	public function __construct(int $maxBufferLength = 8192)
	{
		$sapiStreamEmitter = new SapiStreamEmitter($maxBufferLength);
		$conditionalEmitter = new class ($sapiStreamEmitter) implements EmitterInterface {
			private $emitter;

			public function __construct(EmitterInterface $emitter)
			{
				$this->emitter = $emitter;
			}

			public function emit(ResponseInterface $response): bool
			{
				if (
					!$response->hasHeader('Content-Disposition')
					&& !$response->hasHeader('Content-Range')
				) {
					return false;
				}

				return $this->emitter->emit($response);
			}
		};

		$this->stack = new EmitterStack();
		$this->stack->push(new SapiEmitter());
		$this->stack->push($conditionalEmitter);
	}

	public function emit(ResponseInterface $response): bool
	{
		return $this->stack->emit($response);
	}
}