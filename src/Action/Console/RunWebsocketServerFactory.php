<?php
namespace T4web\Websocket\Action\Console;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use T4web\Websocket\Server;

class RunWebsocketServerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        $serviceLocator = $controllerManager->getServiceLocator();

        $config = $serviceLocator->get('Config');

        return new RunWebsocketServer(
            $serviceLocator->get(Server::class),
            $config['t4web-websocket']
        );
    }
}
