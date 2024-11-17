<?php

declare(strict_types=1);

namespace FiveOrbs\Core;

interface Plugin
{
	public function load(App $app): void;
}
