<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Channel\Name,
    Channel\Messages,
};
use Innmind\TimeContinuum\PointInTime;
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class Channel
{
    /**
     * @param Maybe<PointInTime> $idleSince
     */
    private function __construct(
        private Name $name,
        private VHost\Name $vhost,
        private User\Name $user,
        private int $number,
        private Node\Name $node,
        private State $state,
        private Messages $messages,
        private Count $consumers,
        private bool $confirm,
        private bool $transactional,
        private Maybe $idleSince,
    ) {
    }

    /**
     * @psalm-pure
     *
     * @param Maybe<PointInTime> $idleSince
     */
    #[\NoDiscard]
    public static function of(
        Name $name,
        VHost\Name $vhost,
        User\Name $user,
        int $number,
        Node\Name $node,
        State $state,
        Messages $messages,
        Count $consumers,
        bool $confirm,
        bool $transactional,
        Maybe $idleSince,
    ): self {
        return new self(
            $name,
            $vhost,
            $user,
            $number,
            $node,
            $state,
            $messages,
            $consumers,
            $confirm,
            $transactional,
            $idleSince,
        );
    }

    #[\NoDiscard]
    public function name(): Name
    {
        return $this->name;
    }

    #[\NoDiscard]
    public function vhost(): VHost\Name
    {
        return $this->vhost;
    }

    #[\NoDiscard]
    public function user(): User\Name
    {
        return $this->user;
    }

    #[\NoDiscard]
    public function number(): int
    {
        return $this->number;
    }

    #[\NoDiscard]
    public function node(): Node\Name
    {
        return $this->node;
    }

    #[\NoDiscard]
    public function state(): State
    {
        return $this->state;
    }

    #[\NoDiscard]
    public function messages(): Messages
    {
        return $this->messages;
    }

    #[\NoDiscard]
    public function consumers(): Count
    {
        return $this->consumers;
    }

    #[\NoDiscard]
    public function confirm(): bool
    {
        return $this->confirm;
    }

    #[\NoDiscard]
    public function transactional(): bool
    {
        return $this->transactional;
    }

    /**
     * @return Maybe<PointInTime>
     */
    #[\NoDiscard]
    public function idleSince(): Maybe
    {
        return $this->idleSince;
    }
}
