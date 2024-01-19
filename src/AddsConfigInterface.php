<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Core\Exception\OutOfBoundsException;

/** @psalm-api */
trait AddsConfigInterface
{
    protected array $settings = [];

    public function set(string $key, mixed $value): void
    {
        $this->settings[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->settings);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }

        if (func_num_args() > 1) {
            return $default;
        }

        throw new OutOfBoundsException(
            "The configuration key '{$key}' does not exist"
        );
    }
}
