<?php

declare(strict_types=1);

namespace Conia\Core;

function env(string $key, bool|string|null $default = null): mixed
{
    if (func_num_args() > 1) {
        $value = $_ENV[$key] ?? null;

        if ($value === null) {
            return $default;
        }
    } else {
        $value = $_ENV[$key];
    }

    return match (strtolower($value)) {
        'true' => true,
        'false' => false,
        'null' => null,
        default => $value,
    };
}
