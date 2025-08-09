<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\{
    Model\User\Name,
    Model\User\Password,
};
use Innmind\Immutable\Set;

/**
 * @psalm-immutable
 */
final class User
{
    /**
     * @param Set<string> $tags
     */
    private function __construct(
        private Name $name,
        private Password $password,
        private Set $tags,
    ) {
    }

    /**
     * @no-named-arguments
     * @psalm-pure
     */
    public static function of(
        Name $name,
        Password $password,
        string ...$tags,
    ): self {
        return new self(
            $name,
            $password,
            Set::strings(...$tags),
        );
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function password(): Password
    {
        return $this->password;
    }

    /**
     * @return Set<string>
     */
    public function tags(): Set
    {
        return $this->tags;
    }
}
