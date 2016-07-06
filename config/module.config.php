<?php

namespace T4web\Websocket;

return [
    't4web-websocket' => include 't4web-websocket.config.php',
    'console' => include 'console.config.php',

    'controllers' => array(
        'factories' => array(
            Action\Console\RunWebsocketServer::class => Action\Console\RunWebsocketServerFactory::class,
        ),
    ),

    'view_helpers' => array(
        'factories' => array(
            'getWebsocketHost' => View\Helper\GetWebSocketHostFactory::class,
        ),
    ),
];
