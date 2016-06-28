<?php
namespace T4web\Websocket\Action\Console;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\Server as SocketServer;
use Symfony\Component\Process\Process;

class RunWebsocketServerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        $serviceLocator = $controllerManager->getServiceLocator();

        $loop = EventLoopFactory::create();

        $config = $serviceLocator->get('Config');

        return new RunWebsocketServer(
//            $loop,
//            new SocketServer($loop),
//            $config['t4web-queue']['queues']
        );
    }
}
