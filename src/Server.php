<?php

namespace T4web\Websocket;

use SplObjectStorage;
use Exception;
use Zend\EventManager\EventManager;
use Zend\EventManager\Event;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Server implements MessageComponentInterface
{
    /**
     * @var SplObjectStorage
     */
    private $connections;

    /**
     * @var bool
     */
    private $isDebugEnabled;

    /**
     * @var EventManager
     */
    private $eventManager;

    public function __construct(
        EventManager $eventManager,
        $isDebugEnabled = false
    )
    {
        $this->connections = new SplObjectStorage();
        $this->eventManager = $eventManager;
        $this->isDebugEnabled = $isDebugEnabled;

        $this->eventManager->setIdentifiers('Websocket');
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onOpen(ConnectionInterface $connection)
    {
        $this->connections->attach($connection);

        $this->debug('New client connected');
    }

    public function onMessage(ConnectionInterface $connection, $messageAsJson)
    {
        $message = json_decode($messageAsJson, true);

        $this->debug('Income message: ' . var_export($message, true));

        if (!isset($message['event'])) {
            $response = [
                'event' => 'unknownEvent',
                'data' => null,
                'error' => null,
            ];
            $this->debug('Send message: ' . var_export($response, true));
            $connection->send(json_encode($response));
            return;
        }

        $event = new Event($message['event'], $this, $message['data']);
        $results = $this->eventManager->trigger($event);

        $response = [
            'event' => 'pong',
            'data' => $results->last(),
            'error' => null,
        ];
        $this->debug('Send message: ' . var_export($response, true));
        $connection->send(json_encode($response));

        return;
    }

    public function onClose(ConnectionInterface $connection)
    {
        $this->debug('Client close connection');

        if (!isset($this->connections[$connection])) {
            return;
        }

        $this->connections->detach($connection);
    }

    public function onError(ConnectionInterface $connection, Exception $e) {
        $this->debug('Client error: '. $e->getMessage());
        $connection->close();
    }

    private function debug($msg, $prefix = "**Debug: ")
    {
        if ($this->isDebugEnabled) {
            echo $prefix . $msg . PHP_EOL;
        }
    }
}
