<?php

declare(strict_types=1);

namespace Duon\Core;

interface Plugin
{
	public function load(App $app): void;
}