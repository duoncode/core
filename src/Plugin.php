<?php

declare(strict_types=1);

namespace Conia\Core;

interface Plugin
{
    public function load(App $app): void;
}
