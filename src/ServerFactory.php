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
            $serviceLocator,
            $config['t4web-websocket']['event-handlers'],
            $config['t4web-websocket']['server']['debug-enable']
        );
    }
}
