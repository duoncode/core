<?php

declare(strict_types=1);

namespace Conia\Core\Tests;

use Conia\Core\App;
use Conia\Core\Config;
use Conia\Core\Factory;
use Conia\Core\Factory\Nyholm;
use Conia\Core\Tests\Fixtures\TestContainer;
use Conia\Log\Logger;
use Conia\Registry\Registry;
use Conia\Route\Group;
use Conia\Route\Route;
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
        $this->assertInstanceOf(App::class, App::create(new Config(), new Nyholm()));
    }

    public function testHelperMethods(): void
    {
        $app = App::create(new Config(), new Nyholm());

        $this->assertInstanceOf(Registry::class, $app->registry());
        $this->assertInstanceOf(Router::class, $app->router());
        $this->assertInstanceOf(Config::class, $app->config());
        $this->assertInstanceOf(Factory::class, $app->factory());
        $this->assertInstanceOf(Nyholm::class, $app->factory());
    }

    public function testCreateWithThirdPartyContainer(): void
    {
        $container = new TestContainer();
        $container->add('external', new stdClass());
        $app = App::create(new Config(), new Nyholm(), $container);

        $this->assertInstanceof(stdClass::class, $app->registry()->get('external'));
    }

    public function testMiddlewareHelper(): void
    {
        $middleware = new class () implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($request);
            }
        };
        $app = App::create(new Config(), new Nyholm());
        $app->middleware($middleware);

        $this->assertSame(1, count($app->getMiddleware()));
        $this->assertSame($middleware, $app->getMiddleware()[0]);
    }

    public function testStaticRouteHelper(): void
    {
        $app = App::create(new Config(), new Nyholm());
        error_log("{$this->root}/public/static");
        $app->staticRoute('/static', "{$this->root}/public/static", 'static');
        $app->staticRoute('/unnamedstatic', "{$this->root}/public/static");

        $this->assertSame('/static/test.json', $app->router()->staticUrl('static', 'test.json'));
        $this->assertSame('/unnamedstatic/test.json', $app->router()->staticUrl('/unnamedstatic', 'test.json'));
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

    public function testAppAddRouteAddGroupHelper(): void
    {
        $app = $this->app();
        $route = new Route('/albums', 'Chuck\Tests\Fixtures\TestController::textView', 'albums');
        $group = new Group('/albums', function (Group $group) {
            $ctrl = TestController::class;
            $group->addRoute(Route::get('/{name}', "{$ctrl}::albumName", 'name'));
        }, 'albums:');
        $app->addRoute($route);
        $app->addGroup($group);

        $this->assertSame('/albums', $app->router()->routeUrl('albums'));
        $this->assertSame('/albums/symbolic', $app->router()->routeUrl('albums:name', ['name' => 'symbolic']));
    }

    public function testAppRouteHelper(): void
    {
        $app = $this->app();
        $app->route('/albums', 'Chuck\Tests\Fixtures\TestController::textView', 'albums');

        $this->assertSame('/albums', $app->router()->routeUrl('albums'));
    }

    public function testAppRoutesHelper(): void
    {
        $app = $this->app();
        $app->routes(function (Router $r): void {
            $r->get('/albums', 'Chuck\Tests\Fixtures\TestController::textView', 'albums');
        });

        $this->assertSame('/albums', $app->router()->routeUrl('albums'));
    }

    public function testAppGetHelper(): void
    {
        $app = $this->app();
        $app->get('/albums', 'Chuck\Tests\Fixtures\TestController::textView', 'albums');

        $this->assertSame('/albums', $app->router()->routeUrl('albums'));
    }

    public function testAppPostHelper(): void
    {
        $app = $this->app();
        $app->post('/albums', 'Chuck\Tests\Fixtures\TestController::textView', 'albums');

        $this->assertSame('/albums', $app->router()->routeUrl('albums'));
    }

    public function testAppPutHelper(): void
    {
        $app = $this->app();
        $app->put('/albums', 'Chuck\Tests\Fixtures\TestController::textView', 'albums');

        $this->assertSame('/albums', $app->router()->routeUrl('albums'));
    }

    public function testAppPatchHelper(): void
    {
        $app = $this->app();
        $app->patch('/albums', 'Chuck\Tests\Fixtures\TestController::textView', 'albums');

        $this->assertSame('/albums', $app->router()->routeUrl('albums'));
    }

    public function testAppDeleteHelper(): void
    {
        $app = $this->app();
        $app->delete('/albums', 'Chuck\Tests\Fixtures\TestController::textView', 'albums');

        $this->assertSame('/albums', $app->router()->routeUrl('albums'));
    }

    public function testAppHeadHelper(): void
    {
        $app = $this->app();
        $app->head('/albums', 'Chuck\Tests\Fixtures\TestController::textView', 'albums');

        $this->assertSame('/albums', $app->router()->routeUrl('albums'));
    }

    public function testAppOptionsHelper(): void
    {
        $app = $this->app();
        $app->options('/albums', 'Chuck\Tests\Fixtures\TestController::textView', 'albums');

        $this->assertSame('/albums', $app->router()->routeUrl('albums'));
    }

    public function testAppGroupHelper(): void
    {
        $app = $this->app();
        $app->group('/albums', function (Group $group) {
            $ctrl = TestController::class;
            $group->addRoute(Route::get('/{name}', "{$ctrl}::albumName", 'name'));
        }, 'albums:');

        $this->assertSame('/albums/symbolic', $app->router()->routeUrl('albums:name', ['name' => 'symbolic']));
    }

    public function testAddLogger(): void
    {
        $app = $this->app();
        $app->logger(function (): PsrLogger {
            $logfile = $this->root . '/log/' . bin2hex(random_bytes(4)) . '.log';

            return new Logger($logfile);
        });
        $registry = $app->registry();
        $logger = $registry->get(PsrLogger::class);

        $this->assertInstanceOf(Logger::class, $logger);

        $logger2 = $registry->get(PsrLogger::class);

        $this->assertSame(true, $logger === $logger2);
    }

    public function testRegistryInitialized(): void
    {
        $app = $this->app();
        $registry = $app->registry();

        $this->assertInstanceof(Config::class, $registry->get(Config::class));
        $this->assertInstanceof(Router::class, $registry->get(Router::class));
        $this->assertInstanceof(Factory::class, $registry->get(Factory::class));
    }
}
