<?php

declare(strict_types=1);

namespace FiveOrbs\Core\Tests\Fixtures;

use FiveOrbs\Core\AddsConfigInterface;
use FiveOrbs\Core\ConfigInterface;

class TestConfig implements ConfigInterface
{
	use AddsConfigInterface;
}
