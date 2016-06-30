# Websocket
ZF2 Module. Websocket server build on [Ratchet](https://github.com/ratchetphp/Ratchet)

### Instalation

Just `require t4web/websocket` and add to your `application.config.php` `T4web\Websocket`.

### Run

In console run
```shell
$ php public/index.php websocket start
```

### Configuration

Add in your `global.config.php` :
```php
't4web-websocket' => [
    'server' => [
        // required
        'port' => 8088,

        // not required, default 0
        'debug-enable' => 1,
    ],
];
```

### Quick usage

For testing create html (or copy from https://github.com/t4web/Websocket/blob/master/ws-test.html):
```html
<html lang="en">
    <body>
        <input type="text" id="input" placeholder="Message…" />
        <hr />
        <pre id="output"></pre>

        <script>
            var host   = 'ws://127.0.0.1:8088';
            var socket = null;
            var input  = document.getElementById('input');
            var output = document.getElementById('output');
            var print  = function (message) {
                var samp       = document.createElement('samp');
                samp.innerHTML = message + '\n';
                output.appendChild(samp);
                return;
            };

            input.addEventListener('keyup', function (evt) {
                if (13 !== evt.keyCode) {
                    return;
                }

                var msg = {
                    'event' : 'ping',
                    'data': {message: input.value}
                };

                if (!msg) {
                    return;
                }

                try {
                    print('Send: ' + JSON.stringify(msg, null, 4));
                    socket.send(JSON.stringify(msg));
                    input.value = '';
                    input.focus();
                } catch (e) {
                    console.log(e);
                }

                return;
            });

            try {
                socket = new WebSocket(host);
                socket.onopen = function () {
                    print('connection is opened');
                    input.focus();
                    return;
                };
                socket.onmessage = function (msg) {
                    print('Receive: ' + JSON.stringify(JSON.parse(msg.data), null, 4));
                    return;
                };
                socket.onclose = function () {
                    print('connection is closed');
                    return;
                };
            } catch (e) {
                console.log(e);
            }
        </script>
    </body>
</html>
```

Or from PHP:
```php
/** @var \T4web\Websocket\WebsocketClient $wsClient */
$wsClient = $this->getServiceLocator()->get(\T4web\Websocket\Client::class);
$wsClient->send('ping', ['message' => 'Hello World']);
```

### Proposed architecture

Each websocket request message must be json object:
```json
{
    "event": "event-name",
    "data": { "field": "value" }
}
```

Websocket response message:
```json
{
    "event": "event-name",
    "data": { "field": "value" },
    "error": "string|null"
}
```

Each websocket request mesage - you can handle by attach to `Websocket:event-name` event (trigered by `Zend\EventManager`).
We recommend use [t4web/event-subscriber](https://github.com/t4web/EventSubscriber) module: describe `events` config
with own handlers. `module.config.php`:
```php
'events' => [
    // Event identifier
    'Websocket' => [

        // event name
        'ping' => [

            // handler - Callable object
            Handler\Ping::class,
        ],
    ],
],
```

`Handler\Ping` :
```php
class Ping
{
    public function __invoke(\Zend\EventManager\EventInterface $event)
    {
        return [
            'message' => $event->getParam('message'),
        ];
    }
}
```