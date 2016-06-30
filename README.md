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
                    'data': input.value
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
