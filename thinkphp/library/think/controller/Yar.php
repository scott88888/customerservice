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

namespace think\controller;

/**
 * ThinkPHP Yar控制器類
 */
abstract class Yar
{

    /**
     * 构造函數
     * @access public
     */
    public function __construct()
    {
        //控制器初始化
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }

        //判断扩展是否存在
        if (!extension_loaded('yar')) {
            throw new \Exception('not support yar');
        }

        //實例化Yar_Server
        $server = new \Yar_Server($this);
        // 启動server
        $server->handle();
    }

    /**
     * 魔术方法 有不存在的操作的时候執行
     * @access public
     * @param string $method 方法名
     * @param array $args 参數
     * @return mixed
     */
    public function __call($method, $args)
    {}
}
