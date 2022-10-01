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
    private Name $name;
    private VHost\Name $vhost;
    private User\Name $user;
    private int $number;
    private Node\Name $node;
    private State $state;
    private Messages $messages;
    private Count $consumers;
    private bool $confirm;
    private bool $transactional;
    /** @var Maybe<PointInTime> */
    private Maybe $idleSince;

    /**
     * @param Maybe<PointInTime> $idleSince
     */
    private function __construct(
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
    ) {
        $this->name = $name;
        $this->vhost = $vhost;
        $this->user = $user;
        $this->number = $number;
        $this->node = $node;
        $this->state = $state;
        $this->messages = $messages;
        $this->consumers = $consumers;
        $this->confirm = $confirm;
        $this->transactional = $transactional;
        $this->idleSince = $idleSince;
    }

    /**
     * @psalm-pure
     *
     * @param Maybe<PointInTime> $idleSince
     */
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

    public function name(): Name
    {
        return $this->name;
    }

    public function vhost(): VHost\Name
    {
        return $this->vhost;
    }

    public function user(): User\Name
    {
        return $this->user;
    }

    public function number(): int
    {
        return $this->number;
    }

    public function node(): Node\Name
    {
        return $this->node;
    }

    public function state(): State
    {
        return $this->state;
    }

    public function messages(): Messages
    {
        return $this->messages;
    }

    public function consumers(): Count
    {
        return $this->consumers;
    }

    public function confirm(): bool
    {
        return $this->confirm;
    }

    public function transactional(): bool
    {
        return $this->transactional;
    }

    /**
     * @return Maybe<PointInTime>
     */
    public function idleSince(): Maybe
    {
        return $this->idleSince;
    }
}
