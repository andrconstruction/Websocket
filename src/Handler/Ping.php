<?php
namespace T4web\Websocket\Handler;

use Zend\EventManager\EventInterface;

class Ping
{
    public function __invoke(EventInterface $event)
    {
        return [
            'message' => $event->getParam('message'),
        ];
    }
}