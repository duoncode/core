# Duon Core Framework

<!-- prettier-ignore-start -->
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/9befc54495a54078924928e9caccadd8)](https://app.codacy.com/gh/duoncode/core/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/9befc54495a54078924928e9caccadd8)](https://app.codacy.com/gh/duoncode/core/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_coverage)
[![Psalm level](https://shepherd.dev/github/duoncode/core/level.svg?)](https://duon.sh/core)
[![Psalm coverage](https://shepherd.dev/github/duoncode/core/coverage.svg?)](https://shepherd.dev/github/duoncode/core)
<!-- prettier-ignore-end -->

Duon Core is a lightweight and easily extendable >=PHP 8.3 web framework.

> [!WARNING] This library is under active development, some of its features are still experimental and subject to change. Large parts of the documentation are missing.

It features:

- Http Routing.
- An autowiring container used for automatic dependency injection.
- Middleware.
- Convenience wrappers for PSR request, response and middleware.
- Logging.

## Routing

`App` exposes the router's common route helpers and runs requests through the router `RoutingHandler` internally.

```php
use Duon\Core\App;
use Duon\Router\Group;

$app = App::create();

$app->get('/health', [HealthController::class, 'show'], 'health');
$app->map(['GET', 'POST'], '/login', [AuthController::class, 'login'], 'login');
$app->any('/webhook', $webhook, 'webhook');

$app->group('/admin', function (Group $admin) use ($auth): void {
	$admin->middleware($auth);
	$admin->controller(AdminController::class);

	$admin->get('', 'index', 'admin.index');
	$admin->post('/login', 'login', 'admin.login');
});
```

Supported PSRs:

- PSR-3 Logger Interface
- PSR-4 Autoloading
- PSR-7 Http Messages (Request, Response, Stream, and so on.)
- PSR-11 Container Interface
- PSR-12 Extended Coding Style
- PSR-15 Http Middleware
- PSR-17 Http Factories

## License

This project is licensed under the [MIT license](LICENSE.md).
