<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Node;

use Innmind\RabbitMQ\Management\Exception\InvalidName;
use Innmind\Url\Authority\Host;
use Innmind\Immutable\Str;

final class Name
{
    private const PATTERN = '~^rabbit@(?<host>.*)$~';

    private string $value;
    private Host $host;

    public function __construct(string $value)
    {
        $value = Str::of($value);

        if (!$value->matches(self::PATTERN)) {
            throw new InvalidName($value->toString());
        }

        $this->value = $value->toString();
        $this->host = Host::of(
            $value->capture(self::PATTERN)->get('host')->toString(),
        );
    }

    public function host(): Host
    {
        return $this->host;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
