# RabbitMQManagement

[![Build Status](https://github.com/innmind/rabbitmqmanagement/workflows/CI/badge.svg?branch=master)](https://github.com/innmind/rabbitmqmanagement/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/innmind/rabbitmqmanagement/branch/develop/graph/badge.svg)](https://codecov.io/gh/innmind/rabbitmqmanagement)
[![Type Coverage](https://shepherd.dev/github/innmind/rabbitmqmanagement/coverage.svg)](https://shepherd.dev/github/innmind/rabbitmqmanagement)

Wrapper for the `rabbitmqadmin` command.

## Installation

```sh
composer require innmind/rabbitmq-management
```

## Usage

```php
use Innmind\RabbitMQ\Management\{
    Status,
    Control,
};
use Innmind\OperatingSystem\Factory;

$os = Factory::build();
$status = Status::of(
    $os->control(),
    $os->clock(),
);
$status->users();
$status->vhosts();
$status->connections();
$status->exchanges();
$status->permissions();
$status->channels();
$status->consumers();
$status->queues();
$status->nodes();

$control = Control::of($os->control());
$control->users();
$control->vhosts();
$control->permissions();
```

Essentially this will run `rabbitmqadmin list {element}` on the server and extract informations.

If you need to list the information of a remote server, then you can simply do this:

```php
use Innmind\RabbitMQ\Management\Status\Environment\Remote;
use Innmind\Url\{
    Authority\Host,
    Authority\Port,
    Path,
};

Status::of(
    $os->control(),
    $os->clock(),
    Remote::of(
        Host::of('your-host'),
        Port::of(1337),
        'username',
        'password',
        Path::of('/some-vhost'),
    ),
);
```

However for this to work you need to have `rabbitmqadmin` installed on the machine this code will run.

In case you don't have the command on the machine, you can replace `$os->control()` by [`$os->remote()->ssh(/*...*/)`](https://github.com/Innmind/OperatingSystem#want-to-execute-commands-on-a-remote-server-) so it will use `ssh` to run the command on the machine (and you will need to remove the third argument from `Status::of()`).
