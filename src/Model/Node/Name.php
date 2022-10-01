<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Node;

use Innmind\RabbitMQ\Management\Exception\InvalidName;
use Innmind\Url\Authority\Host;
use Innmind\Immutable\Str;

/**
 * @psalm-immutable
 */
final class Name
{
    private string $value;
    private Host $host;

    public function __construct(string $value)
    {
        $this->value = $value;
        $this->host = Str::of($value)
            ->capture('~^rabbit@(?<host>.*)$~')
            ->get('host')
            ->map(static fn($host) => $host->toString())
            ->map(Host::of(...))
            ->match(
                static fn($host) => $host,
                static fn() => throw new InvalidName($value),
            );
    }

    public function host(): Host
    {
        return $this->host;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
