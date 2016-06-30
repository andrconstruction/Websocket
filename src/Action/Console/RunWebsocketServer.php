<?php
namespace T4web\Websocket\Action\Console;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Json\Json;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use T4web\Websocket\Server;

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
        $this->config = array_merge(
            [
                'server' => [
                    'debug-enable' => 0,
                ],
            ],
            $config
        );
    }

    public function onDispatch(MvcEvent $e)
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Server($this->config['server']['debug-enable'])
                )
            ),
            $this->config['server']['port']
        );

        echo "server started on port " . $this->config['server']['port'] . PHP_EOL;

        $server->run();
    }
}
