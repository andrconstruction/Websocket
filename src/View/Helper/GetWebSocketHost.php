<?php
namespace T4web\Websocket\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Ratchet\Client as RatchetClient;

class GetWebSocketHost extends AbstractHelper
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function __invoke()
    {
        return "ws://" . $this->config['client']['host'] . ":" . $this->config['client']['port'];
    }
}