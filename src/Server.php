<?php

namespace T4web\Websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use Exception;

class Server implements MessageComponentInterface {

    /**
     * @var SplObjectStorage
     */
    private $connections;

    /**
     * @var array
     */
    private $authorized = array();

    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function onOpen(ConnectionInterface $connection)
    {
        $this->connections->attach($connection);

        if ($this->isRequestFromPHPWebSocketClient($connection)) {
            $connection->send(json_encode(array(
                'event' => 'WebSocketClientConnect',
                'data' => (object)array(),
                'error' => null
            )));
        }
    }

    public function onMessage(ConnectionInterface $connection, $messageAsJson)
    {

        $message = json_decode($messageAsJson, true);

        if (!isset($message['event'])) {
            $connection->send(json_encode(array(
                'event' => 'unknownEvent',
                'data' => null,
                'error' => null,
            )));
            $connection->close();
            return;
        }

        if ($message['event'] == 'authorization') {

//            if ($this->authorizationService->isAuthorized($message['data']['accessToken'])) {

                $connection->send(json_encode(array(
                    'event' => 'authorizationSuccess',
                    'data' => (object)array(),
                    'error' => null,
                    'id' => (isset($message['id'])) ? $message['id'] : null,
                )));

                $accessToken = $this->authorizationService->getAccessToken();
                $userId = $accessToken->getUserId();

                if (!isset($this->authorized[$userId])) {
                    $this->authorized[$userId] = array();
                }

                $this->authorized[$userId][$connection->resourceId] = array(
                    'accessToken' => $message['data']['accessToken'],
                    //'timeZone' => $accessToken->getTimeZone(),
                    'uid' => $userId,
                    'connection' => $connection
                );

                $this->connections[$connection] = array(
                    'accessToken' => $message['data']['accessToken'],
                    'uid' => $userId,
                );

//            } else {
//                $connection->send(json_encode(array(
//                    'event' => 'authorizationError',
//                    'data' => null,
//                    'error' => $this->authorizationService->getError(),
//                    'id' => (isset($message['id'])) ? $message['id'] : null,
//                )));
//                $connection->close();
//            }
            return;
        }

        if ($message['event'] == 'ping') {
            return;
        }

        if ($this->isRequestFromPHPWebSocketClient($connection)) {

            if ($message['event'] == Message::TYPE_NEW_CHAT_MESSAGE
                || $message['event'] == Message::TYPE_VIDEO_CHAT_SYSTEM_MESSAGE) {

                if (isset($this->authorized[$message['userId']])) {
                    foreach ($this->authorized[$message['userId']] as $data) {

                        if ($message['event'] == Message::TYPE_NEW_CHAT_MESSAGE ||
                            $message['event'] == Message::TYPE_CHAT_UPDATE_MESSAGE ||
                            $message['event'] == Message::TYPE_CHAT_DELETE_MESSAGE) {
                        }

                        $data['connection']->send(json_encode(array(
                            'event' => $message['event'],
                            'data' => $message['data'],
                            'error' => null
                        )));
                    }
                }

                $connection->close();
            }

            if ($message['event'] == 'getAuthorizedUserIds') {
                $authorizedUserIds = array();
                foreach ($this->authorized as $userId => $connections) {
                    foreach ($connections as $connectionData) {
                        $authorizedUserIds[] = $connectionData['uid'];
                    }
                }

                $connection->send(json_encode(array(
                    'event' => $message['event'],
                    'data' => $authorizedUserIds,
                    'error' => null
                )));
            }
        }
    }

    public function onClose(ConnectionInterface $connection) {

        if (!isset($this->connections[$connection])) {
            return;
        }

        if (isset($this->connections[$connection]['uid'])) {
            if (isset($this->authorized[$this->connections[$connection]['uid']][$connection->resourceId])) {
                unset($this->authorized[$this->connections[$connection]['uid']][$connection->resourceId]);
            }
        }

        $this->connections->detach($connection);
    }

    public function onError(ConnectionInterface $connection, Exception $e) {
        $connection->close();
    }

    private function isRequestFromPHPWebSocketClient(ConnectionInterface $connection) {
        return (string)$connection->WebSocket->request->getHeader('user-agent') == "PHPWebSocketClient";
    }

}
