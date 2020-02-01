<?php
declare(strict_types = 1);

namespace Innmind\RabbitMQ\Management;

use Innmind\Immutable\Set;

interface Status
{
    /**
     * @return Set<User>
     */
    public function users(): Set;

    /**
     * @return Set<VHost>
     */
    public function vhosts(): Set;

    /**
     * @return Set<Connection>
     */
    public function connections(): Set;

    /**
     * @return Set<Exchange>
     */
    public function exchanges(): Set;

    /**
     * @return Set<Permission>
     */
    public function permissions(): Set;

    /**
     * @return Set<Channel>
     */
    public function channels(): Set;

    /**
     * @return Set<Consumer>
     */
    public function consumers(): Set;

    /**
     * @return Set<Queue>
     */
    public function queues(): Set;

    /**
     * @return Set<Node>
     */
    public function nodes(): Set;
}
