<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\worker;

use Workerman\Worker;

/**
 * Worker控制器扩展類
 */
abstract class Server
{
    protected $worker;
    protected $socket    = '';
    protected $protocol  = 'http';
    protected $host      = '0.0.0.0';
    protected $port      = '2346';
    protected $processes = 4;

    /**
     * 架构函數
     * @access public
     */
    public function __construct()
    {
        // 實例化 Websocket 服务
        $this->worker = new Worker($this->socket ?: $this->protocol . '://' . $this->host . ':' . $this->port);
        // 設定进程數
        $this->worker->count = $this->processes;
        // 初始化
        $this->init();

        // 設定回调
        foreach (['onWorkerStart', 'onConnect', 'onMessage', 'onClose', 'onError', 'onBufferFull', 'onBufferDrain', 'onWorkerStop', 'onWorkerReload'] as $event) {
            if (method_exists($this, $event)) {
                $this->worker->$event = [$this, $event];
            }
        }
        // Run worker
        Worker::runAll();
    }

    protected function init()
    {
    }

}
