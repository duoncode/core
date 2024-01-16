<?php

declare(strict_types=1);

namespace Conia\Core;

use Closure;
use Conia\Core\Factory;
use Conia\Core\Middleware\InitRequest;
use Conia\Error\ErrorRenderer;
use Conia\Error\Handler;
use Conia\Quma\Connection;
use Conia\Quma\Database;
use Conia\Registry\Entry;
use Conia\Registry\Registry;
use Conia\Route\AddsBeforeAfter;
use Conia\Route\AddsRoutes;
use Conia\Route\Dispatcher;
use Conia\Route\Group;
use Conia\Route\Route;
use Conia\Route\RouteAdder;
use Conia\Route\Router;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Log\LoggerInterface as Logger;

/** @psalm-api */
class App implements RouteAdder
{
    use AddsRoutes;
    use AddsBeforeAfter;

    protected readonly Dispatcher $dispatcher;
    protected readonly Database $db;

    public function __construct(
        protected readonly Config $config,
        protected readonly Factory $factory,
        protected readonly Router $router,
        protected readonly Registry $registry,
    ) {
        $this->dispatcher = new Dispatcher();
        $this->initializeRegistry();
    }

    public function plugin(Plugin $plugin): void
    {
        $plugin->load($this);
    }

    public static function create(Config $config, Factory $factory): static
    {
        $app = new static($config, $factory, new Router(), new Registry());
        $app->middleware(new Handler($factory->responseFactory));
        $app->middleware(new InitRequest($config));

        return $app;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function registry(): Registry
    {
        return $this->registry;
    }

    public function factory(): Factory
    {
        return $this->factory;
    }

    /** @psalm-param Closure(Router $router):void $creator */
    public function routes(Closure $creator, string $cacheFile = '', bool $shouldCache = true): void
    {
        $this->router->routes($creator, $cacheFile, $shouldCache);
    }

    public function addRoute(Route $route): Route
    {
        return $this->router->addRoute($route);
    }

    public function addGroup(Group $group): void
    {
        $this->router->addGroup($group);
    }

    public function group(
        string $patternPrefix,
        Closure $createClosure,
        string $namePrefix = '',
    ): Group {
        $group = new Group($patternPrefix, $createClosure, $namePrefix);
        $this->router->addGroup($group);

        return $group;
    }

    public function staticRoute(
        string $prefix,
        string $path,
        string $name = '',
    ): void {
        $this->router->addStatic($prefix, $path, $name);
    }

    public function middleware(Middleware ...$middleware): void
    {
        $this->dispatcher->middleware(...$middleware);
    }

    public function logger(callable $callback): void
    {
        $this->registry->add(Logger::class, Closure::fromCallable($callback));
    }

    /**
     * @psalm-param non-empty-string $key
     * @psalm-param class-string|object $value
     */
    public function register(string $key, object|string $value): Entry
    {
        return $this->registry->add($key, $value);
    }

    public function initializeRegistry(): void
    {
        $this->registry->add(Router::class, $this->router);
        $this->registry->add($this->router::class, $this->router);

        $this->registry->add(Factory::class, $this->factory);
        $this->registry->add($this->factory::class, $this->factory);
    }

    public function run(): Response|false
    {
        $request = $this->factory->serverRequest();
        $route = $this->router->match($request);
        $this->dispatcher->setBeforeHandlers($this->beforeHandlers);
        $this->dispatcher->setAfterHandlers($this->afterHandlers);
        $response = $this->dispatcher->dispatch($request, $route);

        return (new Emitter())->emit($response) ? $response : false;
    }
}
