<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use FiveOrbs\Core\App;
use FiveOrbs\Core\Factory;
use FiveOrbs\Core\Factory\Guzzle;
use FiveOrbs\Core\Request;
use FiveOrbs\Core\Response;
use FiveOrbs\Router\After;

$factory = new Guzzle();
$afterHandler = new class ($factory) implements After {
	public function __construct(protected Factory $factory) {}

	public function handle(mixed $data): mixed
	{
		return Response::create($this->factory)->body($data);
	}

	public function replace(After $handler): bool
	{
		return false;
	}
};

$app = App::create($factory);

$app->get('/{param}', function (string $param) {
	return $param;
})->after($afterHandler);

$app->get('/', function (Request $request, Factory $factory) {
	$response = $factory->response();
	$response->getBody()->write($request->origin());

	return $response;
});