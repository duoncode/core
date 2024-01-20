<?php

declare(strict_types=1);

namespace Conia\Core\Tests;

use Conia\Core\App;
use Conia\Core\Factory\Nyholm;
use Conia\Core\Tests\Fixtures\TestConfig;
use Conia\Route\Group;
use Conia\Route\Route;
use Conia\Route\Router;

final class AppRoutingTest extends TestCase
{
    public function testStaticRouteHelper(): void
    {
        $app = App::create(new Nyholm(), new TestConfig());
        $app->staticRoute('/static', "{$this->root}/public/static", 'static');
        $app->staticRoute('/unnamedstatic', "{$this->root}/public/static");

        $this->assertSame('/static/test.json', $app->router()->staticUrl('static', 'test.json'));
        $this->assertSame('/unnamedstatic/test.json', $app->router()->staticUrl('/unnamedstatic', 'test.json'));
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
}
