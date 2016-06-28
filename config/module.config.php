<?php

namespace T4web\Websocket;

return [
    'console' => include 'console.config.php',

    'controllers' => array(
        'factories' => array(
            Action\Console\RunWebsocketServer::class => Action\Console\RunWebsocketServerFactory::class,
        ),
    ),
];
