<?php

declare(strict_types=1);

namespace Conia\Core\Tests;

use Conia\Core\App;
use Conia\Core\Factory;
use Conia\Core\Factory\Nyholm;
use Conia\Core\Plugin;
use Conia\Core\Tests\Fixtures\TestConfig;
use Conia\Core\Tests\Fixtures\TestContainer;
use Conia\Core\Tests\Fixtures\TestLogger;
use Conia\Registry\Registry;
use Conia\Route\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface as PsrLogger;
use stdClass;

final class AppTest extends TestCase
{
    public function testCreateHelper(): void
    {
        $this->assertInstanceOf(App::class, App::create(new Nyholm(), new TestConfig()));
    }

    public function testHelperMethods(): void
    {
        $app = App::create(new Nyholm(), new TestConfig());

        $this->assertInstanceOf(Registry::class, $app->registry());
        $this->assertInstanceOf(Router::class, $app->router());
        $this->assertInstanceOf(TestConfig::class, $app->config());
        $this->assertInstanceOf(Factory::class, $app->factory());
        $this->assertInstanceOf(Nyholm::class, $app->factory());
    }

    public function testCreateWithThirdPartyContainer(): void
    {
        $container = new TestContainer();
        $container->add('external', new stdClass());
        $app = App::create(new Nyholm(), new TestConfig(), $container);

        $this->assertInstanceof(stdClass::class, $app->registry()->get('external'));
    }

    public function testMiddlewareHelper(): void
    {
        $middleware = new class () implements MiddlewareInterface {
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                return $handler->handle($request);
            }
        };
        $app = App::create(new Nyholm(), new TestConfig());
        $app->middleware($middleware);

        $this->assertSame(1, count($app->getMiddleware()));
        $this->assertSame($middleware, $app->getMiddleware()[0]);
    }

    public function testAppRun(): void
    {
        $app = $this->app();
        $app->route('/', 'Conia\Core\Tests\Fixtures\TestController::textView');
        ob_start();
        $app->run($this->request());
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertSame('text', $output);
    }

    public function testAppRegisterHelper(): void
    {
        $app = $this->app();
        $app->register('Chuck', 'Schuldiner')->asIs();
        $registry = $app->registry();

        $this->assertSame('Schuldiner', $registry->get('Chuck'));
    }

    public function testAddLoggerInstance(): void
    {
        $app = $this->app();
        $app->logger(new TestLogger());
        $registry = $app->registry();
        $logger = $registry->get(PsrLogger::class);

        $this->assertInstanceOf(TestLogger::class, $logger);
    }

    public function testAddLoggerCallable(): void
    {
        $app = $this->app();
        $app->logger(function (): PsrLogger {
            return new TestLogger();
        });
        $registry = $app->registry();
        $logger = $registry->get(PsrLogger::class);

        $this->assertInstanceOf(TestLogger::class, $logger);
    }

    public function testRegistryInitialized(): void
    {
        $app = $this->app();
        $registry = $app->registry();

        $this->assertInstanceof(TestConfig::class, $registry->get(TestConfig::class));
        $this->assertInstanceof(Router::class, $registry->get(Router::class));
        $this->assertInstanceof(Factory::class, $registry->get(Factory::class));
    }

    public function testLoadPlugin(): void
    {
        $plugin = new class () implements Plugin {
            public function load(App $app): void
            {
                $app->register('test-id', stdClass::class);
            }
        };
        $app = $this->app();
        $app->load($plugin);

        $this->assertInstanceOf(stdClass::class, $app->registry()->get('test-id'));
    }
}
