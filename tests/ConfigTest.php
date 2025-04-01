<?php

declare(strict_types=1);

namespace Duon\Core\Tests;

use Duon\Core\Exception\OutOfBoundsException;
use Duon\Core\Tests\Fixtures\TestConfig;

final class ConfigTest extends TestCase
{
	public function testGettingSetting(): void
	{
		$config = new TestConfig();

		$config->set('album', 'Symbolic');
		$config->set('albums', ['Symbolic', 'Leprosy']);
		$config->set('year', 1983);

		$this->assertSame(true, $config->has('album'));
		$this->assertSame('Symbolic', $config->get('album'));
		$this->assertSame(true, $config->has('albums'));
		$this->assertSame(['Symbolic', 'Leprosy'], $config->get('albums'));
		$this->assertSame(true, $config->has('year'));
		$this->assertSame(1983, $config->get('year'));
		$this->assertSame(false, $config->has('does-not-exist'));
	}

	public function testGettingDefaultValue(): void
	{
		$config = new TestConfig();

		$this->assertSame('default-value', $config->get('does-not-exist', 'default-value'));
	}

	public function testThrowingException(): void
	{
		$this->throws(OutOfBoundsException::class);

		$config = new TestConfig();

		$config->get('does-not-exist');
	}
}