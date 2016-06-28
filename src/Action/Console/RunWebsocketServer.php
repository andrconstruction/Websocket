<?php
namespace T4web\Websocket\Action\Console;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Json\Json;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class RunWebsocketServer extends AbstractActionController
{
    /**
     * @var array
     */
    private $config;

    public function __construct(
        array $config
    )
    {
        $this->config = $config;
    }

    public function onDispatch(MvcEvent $e)
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new WebsocketServer()
                )
            ),
            $this->config['port']
        );

        echo "server started on port " . $this->config['port'] . PHP_EOL;

        $server->run();
    }
}
