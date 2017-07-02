<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management;

use Innmind\Immutable\SetInterface;

interface Status
{
    /**
     * @return SetInterface<User>
     */
    public function users(): SetInterface;

    /**
     * @return SetInterface<VHost>
     */
    public function vhosts(): SetInterface;

    /**
     * @return SetInterface<Connection>
     */
    public function connections(): SetInterface;

    /**
     * @return SetInterface<Exchange>
     */
    public function exchanges(): SetInterface;

    /**
     * @return SetInterface<Permission>
     */
    public function permissions(): SetInterface;

    /**
     * @return SetInterface<Channel>
     */
    public function channels(): SetInterface;

    /**
     * @return SetInterface<Consumer>
     */
    public function consumers(): SetInterface;

    /**
     * @return SetInterface<Queue>
     */
    public function queues(): SetInterface;

    /**
     * @return SetInterface<Node>
     */
    public function nodes(): SetInterface;
}
