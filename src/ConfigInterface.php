<?php

declare(strict_types=1);

namespace FiveOrbs\Core;

/** @psalm-api */
interface ConfigInterface
{
	public function set(string $key, mixed $value): void;

	public function has(string $key): bool;

	public function get(string $key, mixed $default = null): mixed;
}
