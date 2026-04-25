<?php

declare(strict_types=1);

namespace Duon\Core\Tests;

use Duon\Core\Factory\Discovery;
use Duon\Core\Factory\Factory;
use Duon\Core\Factory\Nyholm;

final class FactoryDiscoveryTest extends TestCase
{
	public function testDiscoveryReturnsPreferredInstalledFactory(): void
	{
		$first = Discovery::create();
		$second = Discovery::create();

		$this->assertInstanceOf(Factory::class, $first);
		$this->assertInstanceOf(Nyholm::class, $first);
		$this->assertNotSame($first, $second);
	}

	public function testDiscoverySkipsIncompleteNyholmInstall(): void
	{
		$result = $this->runIsolatedPhp($this->discoveryBootstrap() . <<<'PHP'

			namespace Nyholm\Psr7\Factory {
				final class Psr17Factory {}
			}

			namespace GuzzleHttp\Psr7 {
				final class HttpFactory {}
			}

			namespace Duon\Core\Factory {
				final class Guzzle extends AbstractFactory
				{
					public function serverRequest(): never
					{
						throw new \LogicException();
					}
				}
			}

			namespace {
				echo \Duon\Core\Factory\Discovery::create()::class;
			}
			PHP);

		$this->assertSame(0, $result['status'], $result['output']);
		$this->assertSame('Duon\\Core\\Factory\\Guzzle', $result['output']);
	}

	public function testDiscoveryFailsWithoutSupportedFactory(): void
	{
		$result = $this->runIsolatedPhp($this->discoveryBootstrap() . <<<'PHP'

			namespace {
				try {
					\Duon\Core\Factory\Discovery::create();
					echo 'No exception thrown';
					exit(1);
				} catch (\Duon\Core\Exception\RuntimeException $exception) {
					echo $exception->getMessage();
				}
			}
			PHP);

		$this->assertSame(0, $result['status'], $result['output']);
		$this->assertStringContainsString('No supported PSR-7 implementation found.', $result['output']);
		$this->assertStringContainsString('nyholm/psr7 with nyholm/psr7-server', $result['output']);
	}

	private function discoveryBootstrap(): string
	{
		$root = dirname(__DIR__);

		return sprintf(
			<<<'PHP'
				declare(strict_types=1);

				namespace {
					require %s;
					require %s;
					require %s;
					require %s;
					require %s;
				}
				PHP,
			var_export($root . '/src/Exception/CoreException.php', true),
			var_export($root . '/src/Exception/RuntimeException.php', true),
			var_export($root . '/src/Factory/Factory.php', true),
			var_export($root . '/src/Factory/AbstractFactory.php', true),
			var_export($root . '/src/Factory/Discovery.php', true),
		);
	}

	/**
	 * @return array{status: int, output: string}
	 */
	private function runIsolatedPhp(string $code): array
	{
		$output = [];
		$status = 0;

		exec(PHP_BINARY . ' -r ' . escapeshellarg($code) . ' 2>&1', $output, $status);

		return [
			'status' => $status,
			'output' => implode("\n", $output),
		];
	}
}
