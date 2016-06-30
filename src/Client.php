<?php
namespace T4web\Websocket;

use RuntimeException;
use Zend\Json\Json;
use Ratchet\Client as RatchetClient;
use Ratchet\Client\WebSocket;

/**
 * Produces messages and sends them to the websocket server.
 *
 *     $wsClient = $serviceLocator->get(\T4web\Websocket\Client::class);
 *     $wsClient->send('default', ['message' => 'Hello World']);
 */
class Client
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    public function __construct(array $config)
    {
        $this->host = $config['client']['host'];
        $this->port = $config['client']['port'];
    }

    public function send($event, $data)
    {
        RatchetClient\connect("ws://{$this->host}:{$this->port}")
            ->done(
                function(WebSocket $conn) use ($event, $data) {
                    $conn->on('message', function($msg) use ($conn) {
                        $conn->close();
                    });

                    $conn->send(json_encode(['event' => $event, 'data' => $data]));
                },
                function ($e) {
                    throw new RuntimeException("Could not connect: {$e->getMessage()}\n");
                }
            );
    }
}