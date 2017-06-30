<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\{
    Model\User\Name,
    Model\User\Password,
    Exception\InvalidArgumentException
};
use Innmind\Immutable\SetInterface;

final class User
{
    private $name;
    private $password;
    private $tags;

    public function __construct(
        Name $name,
        Password $password,
        SetInterface $tags
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
     * @return SetInterface<string>
     */
    public function tags(): SetInterface
    {
        return $this->tags;
    }
}
