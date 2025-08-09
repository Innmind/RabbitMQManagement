<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management\Model;

use Innmind\RabbitMQ\Management\Model\{
    Connection\Name,
    Connection\Peer,
    Connection\Timeout,
    Connection\Protocol,
    Connection\AuthenticationMechanism,
    Connection\Type,
};
use Innmind\TimeContinuum\PointInTime;
use Innmind\Url\Authority\{
    Host,
    Port,
};

/**
 * @psalm-immutable
 */
final class Connection
{
    private function __construct(
        private Name $name,
        private PointInTime $connectedAt,
        private Timeout $timeout,
        private VHost\Name $vhost,
        private User\Name $user,
        private Protocol $protocol,
        private AuthenticationMechanism $authenticationMechanism,
        private bool $ssl,
        private Peer $peer,
        private Host $host,
        private Port $port,
        private Node\Name $node,
        private Type $type,
        private State $state,
    ) {
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(
        Name $name,
        PointInTime $connectedAt,
        Timeout $timeout,
        VHost\Name $vhost,
        User\Name $user,
        Protocol $protocol,
        AuthenticationMechanism $authenticationMechanism,
        bool $ssl,
        Peer $peer,
        Host $host,
        Port $port,
        Node\Name $node,
        Type $type,
        State $state,
    ): self {
        return new self(
            $name,
            $connectedAt,
            $timeout,
            $vhost,
            $user,
            $protocol,
            $authenticationMechanism,
            $ssl,
            $peer,
            $host,
            $port,
            $node,
            $type,
            $state,
        );
    }

    #[\NoDiscard]
    public function name(): Name
    {
        return $this->name;
    }

    #[\NoDiscard]
    public function connectedAt(): PointInTime
    {
        return $this->connectedAt;
    }

    #[\NoDiscard]
    public function timeout(): Timeout
    {
        return $this->timeout;
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
    public function protocol(): Protocol
    {
        return $this->protocol;
    }

    #[\NoDiscard]
    public function authenticationMechanism(): AuthenticationMechanism
    {
        return $this->authenticationMechanism;
    }

    #[\NoDiscard]
    public function ssl(): bool
    {
        return $this->ssl;
    }

    #[\NoDiscard]
    public function peer(): Peer
    {
        return $this->peer;
    }

    #[\NoDiscard]
    public function host(): Host
    {
        return $this->host;
    }

    #[\NoDiscard]
    public function port(): Port
    {
        return $this->port;
    }

    #[\NoDiscard]
    public function node(): Node\Name
    {
        return $this->node;
    }

    #[\NoDiscard]
    public function type(): Type
    {
        return $this->type;
    }

    #[\NoDiscard]
    public function state(): State
    {
        return $this->state;
    }
}
