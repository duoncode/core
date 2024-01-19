<?php

declare(strict_types=1);

namespace Conia\Core\Tests\Fixtures;

use Conia\Core\AddsConfigInterface;
use Conia\Core\ConfigInterface;

class TestConfig implements ConfigInterface
{
    use AddsConfigInterface;
}
