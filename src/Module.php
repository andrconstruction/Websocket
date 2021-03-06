<?php

namespace T4web\Websocket;

use Zend\Console\Adapter\AdapterInterface as Console;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src',
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include dirname(__DIR__) . '/config/module.config.php';
    }

    public function getConsoleUsage(Console $console)
    {
        return array(
            // Describe available commands
            'websocket start' => 'Run websocket server',
        );
    }
}

