<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\{
    Model\User\Name,
    Model\User\Password,
    Exception\InvalidArgumentException,
};
use Innmind\Immutable\Set;

final class User
{
    private Name $name;
    private Password $password;
    /** @var Set<string> */
    private Set $tags;

    /**
     * @param Set<string> $tags
     */
    public function __construct(
        Name $name,
        Password $password,
        Set $tags
    ) {
        if ((string) $tags->type() !== 'string') {
            throw new InvalidArgumentException;
        }

        $this->name = $name;
        $this->password = $password;
        $this->tags = $tags;
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
