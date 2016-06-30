<?php

namespace T4web\Websocket;

use SplObjectStorage;
use Exception;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebsocketServer implements MessageComponentInterface
{
    /**
     * @var SplObjectStorage
     */
    private $connections;

    private $isDebugEnabled;

    public function __construct($isDebugEnabled = false)
    {
        $this->connections = new SplObjectStorage();
        $this->isDebugEnabled = $isDebugEnabled;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onOpen(ConnectionInterface $connection)
    {
        $this->connections->attach($connection);

        $this->debug('New client connected');

        if ($this->isRequestFromPHPWebSocketClient($connection)) {
            $connection->send(json_encode(array(
                'event' => 'PHPWebSocketClientConnect',
                'data' => (object)array(),
                'error' => null
            )));
        }
    }

    public function onMessage(ConnectionInterface $connection, $messageAsJson)
    {
        $message = json_decode($messageAsJson, true);

        $this->debug('Income message: ' . var_export($message, true));

        if (!isset($message['event']) || !isset($this->config['server']['events'][$message['event']])) {
            $response = [
                'event' => 'unknownEvent',
                'data' => null,
                'error' => null,
            ];
            $this->debug('Send message: ' . var_export($response, true));
            $connection->send(json_encode($response));
            return;
        }

        if ($message['event'] == 'ping') {
            $response = [
                'event' => 'pong',
                'data' => $message['data'],
                'error' => null,
            ];
            $this->debug('Send message: ' . var_export($response, true));
            $connection->send(json_encode($response));
            return;
        }
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

    private function isRequestFromPHPWebSocketClient(ConnectionInterface $connection) {
        return (string)$connection->WebSocket->request->getHeader('user-agent') == "PHPWebSocketClient";
    }

    private function debug($msg, $prefix = "**Debug: ")
    {
        if ($this->isDebugEnabled) {
            echo $prefix . $msg . PHP_EOL;
        }
    }
}
