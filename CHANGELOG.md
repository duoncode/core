# Changelog

## [Unreleased](https://github.com/duoncode/core/compare/0.2.0...HEAD)

### Breaking

- Moved the `Duon\Core\Factory` interface to `Duon\Core\Factory\Factory`. PSR-17 factory implementations remain in the `Duon\Core\Factory` namespace.
- Removed app-level configuration support, including `ConfigInterface`, `AddsConfigInterface`, `App::config()`, and config arguments in `App::__construct()` and `App::create()`.
- Removed the factory argument from `App::create()`. It now discovers a PSR-17 factory automatically; pass custom factories to the `App` constructor.
- Updated route helpers to match `duon/router`: use `any()` for methodless routes instead of `route()`, use `map()` for explicit method lists, remove the passthrough `routes()` helper, remove the `addGroup()` helper, and make `group()` return `void`.

### Added

- Added `Duon\Core\Factory\Discovery` to select an installed Nyholm, Guzzle, or Laminas PSR-17 factory automatically.
- `App::group()` now delegates to the router callback group API.
- BrowserSync-backed watch mode to the development server with the `--watch` option, configurable watch patterns, brace/glob expansion, and reload debounce settings.

## [0.2.0](https://github.com/duoncode/core/releases/tag/0.2.0) (2026-02-21)

Codename: Jonas

### Changed

- BREAKING: Replaced `duon/registry` dependency with `duon/container`. The `Registry` class is now `Container` (`Duon\Container\Container`), and `App::registry()` is now `App::container()`.

## [0.1.0](https://github.com/duoncode/core/releases/tag/0.1.0) (2026-01-31)

Initial release.

### Added

- Core web framework integrating CLI, container, and router components
- HTTP request/response handling with PSR-7/PSR-15 support
- Application bootstrapping and middleware pipeline
