<?php

namespace T4web\Websocket;

return [
    't4web-websocket' => include 't4web-websocket.config.php',
    'console' => include 'console.config.php',
    'events' => include 'events.config.php',

    'controllers' => array(
        'factories' => array(
            Action\Console\RunWebsocketServer::class => Action\Console\RunWebsocketServerFactory::class,
        ),
    ),
];
