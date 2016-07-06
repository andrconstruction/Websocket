<?php
namespace T4web\Websocket\Handler;

use SplObjectStorage;
use Ratchet\ConnectionInterface;

interface HandlerInterface
{
    /**
     * @param string $eventName
     * @param array $data
     * @param ConnectionInterface $connection
     * @param SplObjectStorage $connections
     * @return void
     */
    public function handle(
        $eventName,
        array $data,
        ConnectionInterface $connection,
        SplObjectStorage $connections
    );
}