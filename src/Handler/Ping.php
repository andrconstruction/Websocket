<?php
namespace T4web\Websocket\Handler;

use SplObjectStorage;
use Zend\EventManager\EventInterface;
use Ratchet\ConnectionInterface;

class Ping implements HandlerInterface
{
    public function handle($eventName, array $data, ConnectionInterface $connection, SplObjectStorage $connections)
    {
        $response = [
            'event' => 'pong',
            'data' => $data,
            'error' => null,
        ];

        $connection->send(json_encode($response));
    }
}