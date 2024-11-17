<?php

declare(strict_types=1);

namespace FiveOrbs\Core\Tests;

use function FiveOrbs\Core\env;

final class FunctionTest extends TestCase
{
	protected function setUp(): void
	{
		global $_ENV;
		$_ENV = [];
	}

	public function testFunctionEnvGetValue(): void
	{
		$_ENV['TEST'] = '1983';

		$this->assertSame('1983', env('TEST'));
	}

	public function testFunctionEnvGetDefaultValue(): void
	{
		$this->assertSame('2001', env('TEST', '2001'));
	}

	public function testFunctionEnvTypeCasting(): void
	{
		$_ENV['TEST'] = 'true';
		$this->assertSame(true, env('TEST'));
		$_ENV['TEST'] = 'True';
		$this->assertSame(true, env('TEST'));
		$_ENV['TEST'] = 'TRUE';
		$this->assertSame(true, env('TEST'));
		$_ENV['TEST'] = 'tRUe';
		$this->assertSame(true, env('TEST'));

		$_ENV['TEST'] = 'false';
		$this->assertSame(false, env('TEST'));
		$_ENV['TEST'] = 'False';
		$this->assertSame(false, env('TEST'));
		$_ENV['TEST'] = 'FALSE';
		$this->assertSame(false, env('TEST'));
		$_ENV['TEST'] = 'faLsE';
		$this->assertSame(false, env('TEST'));

		$_ENV['TEST'] = 'null';
		$this->assertSame(null, env('TEST'));
		$_ENV['TEST'] = 'Null';
		$this->assertSame(null, env('TEST'));
		$_ENV['TEST'] = 'NULL';
		$this->assertSame(null, env('TEST'));
		$_ENV['TEST'] = 'nUlL';
		$this->assertSame(null, env('TEST'));
	}
}
