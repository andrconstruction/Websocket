<?php

namespace T4web\Websocket;

return [
    'router' => [
        'routes' => [
            'websocket-start' => [
                'type' => 'Simple',
                'options' => [
                    'route' => 'websocket start',
                    'defaults' => [
                        'controller' => Action\Console\RunWebsocketServer::class,
                    ]
                ]
            ],
        ],
    ],
];
