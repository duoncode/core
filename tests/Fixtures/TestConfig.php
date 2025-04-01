<?php

declare(strict_types=1);

namespace Duon\Core\Tests\Fixtures;

use Duon\Core\AddsConfigInterface;
use Duon\Core\ConfigInterface;

class TestConfig implements ConfigInterface
{
	use AddsConfigInterface;
}