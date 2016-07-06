<?php

namespace T4web\Websocket;

use SplObjectStorage;
use Exception;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Server implements MessageComponentInterface
{
    /**
     * @var SplObjectStorage
     */
    private $connections;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @var array
     */
    private $handlers;

    /**
     * @var bool
     */
    private $isDebugEnabled;

    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        array $eventHandlers,
        $isDebugEnabled = false
    )
    {
        $this->connections = new SplObjectStorage();
        $this->serviceLocator = $serviceLocator;
        $this->handlers = $eventHandlers;
        $this->isDebugEnabled = $isDebugEnabled;
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

        if (!isset($message['event']) || !isset($this->handlers[$message['event']])) {
            $response = [
                'event' => 'unknownEvent',
                'data' => null,
                'error' => 'event ' . @$message['event'] . ' not described',
            ];
            $this->debug('Send message: ' . var_export($response, true));
            $connection->send(json_encode($response));
            return;
        }

        /** @var Handler\HandlerInterface $handler */
        $handler = $this->serviceLocator->get($this->handlers[$message['event']]);

        if (!($handler instanceof Handler\HandlerInterface)) {
            $response = [
                'event' => 'unknownEvent',
                'data' => null,
                'error' => 'handler for event ' . $message['event'] . ' must be instance of T4web\Websocket\Handler\HandlerInterface',
            ];
            $this->debug('Send message: ' . var_export($response, true));
            $connection->send(json_encode($response));
            return;
        }

        $handler->handle($message['event'], $message['data'], $connection, $this->connections);

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
