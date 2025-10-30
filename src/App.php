<?php

declare(strict_types=1);

namespace Duon\Core;

use Closure;
use Duon\Core\ConfigInterface as Config;
use Duon\Core\Factory;
use Duon\Registry\Entry;
use Duon\Registry\Registry;
use Duon\Router\AddsBeforeAfter;
use Duon\Router\AddsRoutes;
use Duon\Router\Dispatcher;
use Duon\Router\Group;
use Duon\Router\Route;
use Duon\Router\RouteAdder;
use Duon\Router\Router;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Log\LoggerInterface as Logger;

/** @psalm-api */
class App implements RouteAdder
{
	use AddsRoutes;
	use AddsBeforeAfter;

	protected readonly Dispatcher $dispatcher;

	public function __construct(
		protected readonly Factory $factory,
		protected readonly Router $router,
		protected readonly Registry $registry,
		protected readonly ?Config $config = null,
	) {
		$this->dispatcher = new Dispatcher();
		$this->initializeRegistry();
	}

	public function load(Plugin $plugin): void
	{
		$plugin->load($this);
	}

	public static function create(Factory $factory, ?Config $config = null, ?Container $container = null): self
	{
		$app = new self($factory, new Router(), new Registry(container: $container), $config);

		return $app;
	}

	public function router(): Router
	{
		return $this->router;
	}

	public function factory(): Factory
	{
		return $this->factory;
	}

	public function config(): ?Config
	{
		return $this->config;
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

	public function getMiddleware(): array
	{
		return $this->dispatcher->getMiddleware();
	}

	public function middleware(Middleware ...$middleware): void
	{
		$this->dispatcher->middleware(...$middleware);
	}

	public function logger(Logger|callable $logger): void
	{
		if ($logger instanceof Logger) {
			$this->registry->add(Logger::class, $logger);
		} else {
			$this->registry->add(Logger::class, Closure::fromCallable($logger));
		}
	}

	public function registry(): Registry
	{
		return $this->registry;
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

		if ($this->config) {
			$this->registry->add(Config::class, $this->config);
			$this->registry->add($this->config::class, $this->config);
		}
	}

	public function run(?Request $request = null): Response|false
	{
		$request = $request ?? $this->factory->serverRequest();
		$route = $this->router->match($request);
		$this->dispatcher->setBeforeHandlers($this->beforeHandlers);
		$this->dispatcher->setAfterHandlers($this->afterHandlers);
		$response = $this->dispatcher->dispatch($request, $route, $this->registry);

		return (new Emitter())->emit($response) ? $response : false;
	}
}

