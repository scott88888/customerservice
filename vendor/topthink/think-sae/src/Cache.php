<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\sae;

use think\Exception;

/**
 * SAE Memcache快取驱動
 * @author    liu21st <liu21st@gmail.com>
 */
class Cache
{
    protected $handler = null;
    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 11211,
        'expire'     => 0,
        'timeout'    => false,
        'persistent' => false,
        'prefix'     => '',
    ];

    /**
     * 架构函數
     * @param array $options 快取参數
     * @access public
     */
    public function __construct($options = [])
    {
        if (!function_exists('sae_debug')) {
            throw new \BadFunctionCallException('must run at sae');
        }
        $this->handler = new \Memcached();
        if (!$this->handler) {
            throw new Exception('memcache init error');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * 读取快取
     * @access public
     * @param string $name 快取变量名
     * @return mixed
     */
    public function get($name)
    {
        return $this->handler->get($_SERVER['HTTP_APPVERSION'] . '/' . $this->options['prefix'] . $name);
    }

    /**
     * 寫入快取
     * @access public
     * @param string    $name 快取变量名
     * @param mixed     $value  存储資料
     * @param integer   $expire  有效時間（秒）
     * @return bool
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $name = $this->options['prefix'] . $name;
        if ($this->handler->set($_SERVER['HTTP_APPVERSION'] . '/' . $name, $value, $expire)) {
            return true;
        }
        return false;
    }

    /**
     * 刪除快取
     * @param    string  $name 快取变量名
     * @param bool|false $ttl
     * @return bool
     */
    public function rm($name, $ttl = false)
    {
        $name = $_SERVER['HTTP_APPVERSION'] . '/' . $this->options['prefix'] . $name;
        return false === $ttl ?
        $this->handler->delete($name) :
        $this->handler->delete($name, $ttl);
    }

    /**
     * 清除快取
     * @access public
     * @return bool
     */
    public function clear()
    {
        return $this->handler->flush();
    }

    /**
     * 获得SaeKv對象
     */
    private function getKv()
    {
        static $kv;
        if (!$kv) {
            $kv = new \SaeKV();
            if (!$kv->init()) {
                throw new Exception('KVDB init error');
            }
        }
        return $kv;
    }

}
