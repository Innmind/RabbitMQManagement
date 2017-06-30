<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Node;

use Innmind\RabbitMQ\Management\Exception\InvalidName;
use Innmind\Url\Authority\{
    Host,
    HostInterface
};
use Innmind\Immutable\Str;

final class Name
{
    private const PATTERN = '~^rabbit@(?<host>.*)$~';

    private $value;
    private $host;

    public function __construct(string $value)
    {
        $value = new Str($value);

        if (!$value->matches(self::PATTERN)) {
            throw new InvalidName((string) $value);
        }

        $this->value = (string) $value;
        $this->host = new Host(
            (string) $value->capture(self::PATTERN)->get('host')
        );
    }

    public function host(): HostInterface
    {
        return $this->host;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
