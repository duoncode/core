# Changelog

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
