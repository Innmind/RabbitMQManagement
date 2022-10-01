<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model\Node;

use Innmind\RabbitMQ\Management\Exception\DomainException;
use Innmind\Url\Authority\Host;
use Innmind\Immutable\{
    Str,
    Maybe,
};

/**
 * @psalm-immutable
 */
final class Name
{
    private string $value;
    private Host $host;

    private function __construct(string $value, Host $host)
    {
        $this->value = $value;
        $this->host = $host;
    }

    /**
     * @psalm-pure
     *
     * @throws DomainException
     */
    public static function of(string $value): self
    {
        return self::maybe($value)->match(
            static fn($self) => $self,
            static fn() => throw new DomainException($value),
        );
    }

    /**
     * @psalm-pure
     *
     * @return Maybe<self>
     */
    public static function maybe(string $value): Maybe
    {
        return Str::of($value)
            ->capture('~^rabbit@(?<host>.*)$~')
            ->get('host')
            ->map(static fn($host) => $host->toString())
            ->map(Host::of(...))
            ->map(static fn($host) => new self($value, $host));
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
