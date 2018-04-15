# RabbitMQManagement

| `master` | `develop` |
|----------|-----------|
| [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/?branch=master) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/?branch=develop) |
| [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/?branch=master) | [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/?branch=develop) |
| [![Build Status](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/build-status/master) | [![Build Status](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/RabbitMQManagement/build-status/develop) |

Wrapper for the `rabbitmqadmin` command.

## Installation

```sh
composer require innmind/rabbitmq-management
```

## Usage

```php
use Innmind\RabbitMQ\Management\{
    Status\Status,
    Control\Control
};
use Innmind\Server\Control\ServerFactory;
use Innmind\TimeContinuum\TimeContinuum\Earth;

$status = new Status(
    $server = (new ServerFactory)->make(),
    new Earth
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
use Innmind\Url\Authority\{
    Host,
    Port
};

new Status(
    (new ServerFactory)->make(),
    new Earth,
    new Remote(
        new Host('your-host'),
        new Port(1337),
        'username',
        'password'
    )
);
```

However for this to work you need to have `rabbitmqadmin` installed on the machine this code will run.

In case you don't have the command on the machine, you can replace `(new ServerFatory)->make()` by [`new Remote(/*...*/)`](https://github.com/Innmind/ServerControl/blob/develop/src/Servers/Remote.php) so it will use `ssh` to run the command on the machine (and you will need to remove the third argument from `new Status`).
