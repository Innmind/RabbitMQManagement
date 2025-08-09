# Changelog

## [Unreleased]

### Changed

- Drop support for PHP `8.1`
- Requires `innmind/foundation:^1.7.1`
- The following methods now return an `Innmind\Immutable\Attempt<Innmind\Immutable\SideEffect>`:
    - `Innmind\RabbitMQ\Management\Control\Permissions::declare()`
    - `Innmind\RabbitMQ\Management\Control\Permissions::delete()`
    - `Innmind\RabbitMQ\Management\Control\Users::declare()`
    - `Innmind\RabbitMQ\Management\Control\Users::delete()`
    - `Innmind\RabbitMQ\Management\Control\VHosts::declare()`
    - `Innmind\RabbitMQ\Management\Control\VHosts::delete()`

### Fixed

- PHP `8.4` deprecations

## 3.2.0 - 2023-09-23

### Added

- Support for `innmind/immutable:~5.0`

## 3.1.0 - 2023-01-29

### Added

- Support for `innmind/server-control:~5.0`
