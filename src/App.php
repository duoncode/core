<?php

declare(strict_types=1);

namespace Duon\Core;

use Closure;
use Duon\Container\Container;
use Duon\Container\Entry;
use Duon\Core\Factory\Discovery;
use Duon\Core\Factory\Factory;
use Duon\Router\AddsBeforeAfter;
use Duon\Router\AddsRoutes;
use Duon\Router\Dispatcher;
use Duon\Router\Group;
use Duon\Router\Route;
use Duon\Router\RouteAdder;
use Duon\Router\Router;
use Override;
use Psr\Container\ContainerInterface as PsrContainer;
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
		protected readonly Container $container,
	) {
		$this->dispatcher = new Dispatcher();
		$this->initializeContainer();
	}

	public function load(Plugin $plugin): void
	{
		$plugin->load($this);
	}

	public static function create(?PsrContainer $container = null): self
	{
		return new self(
			Discovery::create(),
			new Router(),
			new Container(container: $container),
		);
	}

	public function router(): Router
	{
		return $this->router;
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

	#[Override]
	public function addRoute(Route $route): Route
	{
		return $this->router->addRoute($route);
	}

	#[Override]
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
			$this->container->add(Logger::class, $logger);
		} else {
			$this->container->add(Logger::class, Closure::fromCallable($logger));
		}
	}

	public function container(): Container
	{
		return $this->container;
	}

	/**
	 * @psalm-param non-empty-string $key
	 * @psalm-param class-string|object $value
	 */
	public function register(string $key, object|string $value): Entry
	{
		return $this->container->add($key, $value);
	}

	public function initializeContainer(): void
	{
		$this->container->add(Router::class, $this->router);
		$this->container->add($this->router::class, $this->router);

		$this->container->add(Factory::class, $this->factory);
		$this->container->add($this->factory::class, $this->factory);
	}

	public function run(?Request $request = null): Response|false
	{
		$request ??= $this->factory->serverRequest();
		$route = $this->router->match($request);
		$this->dispatcher->setBeforeHandlers($this->beforeHandlers);
		$this->dispatcher->setAfterHandlers($this->afterHandlers);
		$response = $this->dispatcher->dispatch($request, $route, $this->container);

		return new Emitter()->emit($response) ? $response : false;
	}
}
