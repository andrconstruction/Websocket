<?php

namespace T4web\Websocket;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\EventManager;

class ServerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        return new Server(
            new EventManager(),
            $config['t4web-websocket']['server']['debug-enable']
        );
    }
}
