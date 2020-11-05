# RabbitMQManagement

| `develop` |
|-----------|
| [![codecov](https://codecov.io/gh/Innmind/RabbitMQManagement/branch/develop/graph/badge.svg)](https://codecov.io/gh/Innmind/RabbitMQManagement) |
| [![Build Status](https://github.com/Innmind/RabbitMQManagement/workflows/CI/badge.svg)](https://github.com/Innmind/RabbitMQManagement/actions?query=workflow%3ACI) |

Wrapper for the `rabbitmqadmin` command.

## Installation

```sh
composer require innmind/rabbitmq-management
```

## Usage

```php
use Innmind\RabbitMQ\Management\{
    Status\Status,
    Control\Control,
};
use Innmind\Server\Control\ServerFactory;
use Innmind\TimeContinuum\Earth\Clock;

$status = new Status(
    $server = ServerFactory::build(),
    new Clock,
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

$control = new Control($server);
$control->users();
$control->vhosts();
$control->permissions();
```

Essentially this will run `rabbitmqadmin list {element}` on the server and extract informations.

If you need to list the information of a remote server, then you cal simply do this:

```php
use Innmind\RabbitMQ\Management\Status\Environment\Remote;
use Innmind\Url\{
    Authority\Host,
    Authority\Port,
    Path,
};

new Status(
    ServerFactory::build(),
    new Clock,
    new Remote(
        Host::of('your-host'),
        Port::of(1337),
        'username',
        'password',
        Path::of('/some-vhost'),
    ),
);
```

However for this to work you need to have `rabbitmqadmin` installed on the machine this code will run.

In case you don't have the command on the machine, you can replace `ServerFatory::build()` by [`new Remote(/*...*/)`](https://github.com/Innmind/ServerControl/blob/develop/src/Servers/Remote.php) so it will use `ssh` to run the command on the machine (and you will need to remove the third argument from `new Status`).
