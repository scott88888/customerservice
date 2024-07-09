<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;

use think\cache\Driver;

class Cache
{
    /**
     * @var array 快取的實例
     */
    public static $instance = [];

    /**
     * @var int 快取读取次數
     */
    public static $readTimes = 0;

    /**
     * @var int 快取寫入次數
     */
    public static $writeTimes = 0;

    /**
     * @var object 操作句柄
     */
    public static $handler;

    /**
     * 連結快取驱動
     * @access public
     * @param  array       $options 配置數组
     * @param  bool|string $name    快取連結標識 true 强制重新連結
     * @return Driver
     */
    public static function connect(array $options = [], $name = false)
    {
        $type = !empty($options['type']) ? $options['type'] : 'File';

        if (false === $name) {
            $name = md5(serialize($options));
        }

        if (true === $name || !isset(self::$instance[$name])) {
            $class = false === strpos($type, '\\') ?
            '\\think\\cache\\driver\\' . ucwords($type) :
            $type;

            // 记录初始化訊息
            App::$debug && Log::record('[ CACHE ] INIT ' . $type, 'info');

            if (true === $name) {
                return new $class($options);
            }

            self::$instance[$name] = new $class($options);
        }

        return self::$instance[$name];
    }

    /**
     * 自動初始化快取
     * @access public
     * @param  array $options 配置數组
     * @return Driver
     */
    public static function init(array $options = [])
    {
        if (is_null(self::$handler)) {
            if (empty($options) && 'complex' == Config::get('cache.type')) {
                $default = Config::get('cache.default');
                // 取得默認快取配置，並連結
                $options = Config::get('cache.' . $default['type']) ?: $default;
            } elseif (empty($options)) {
                $options = Config::get('cache');
            }

            self::$handler = self::connect($options);
        }

        return self::$handler;
    }

    /**
     * 切换快取類型 需要配置 cache.type 為 complex
     * @access public
     * @param  string $name 快取標識
     * @return Driver
     */
    public static function store($name = '')
    {
        if ('' !== $name && 'complex' == Config::get('cache.type')) {
            return self::connect(Config::get('cache.' . $name), strtolower($name));
        }

        return self::init();
    }

    /**
     * 判断快取是否存在
     * @access public
     * @param  string $name 快取变量名
     * @return bool
     */
    public static function has($name)
    {
        self::$readTimes++;

        return self::init()->has($name);
    }

    /**
     * 读取快取
     * @access public
     * @param  string $name    快取標識
     * @param  mixed  $default 默認值
     * @return mixed
     */
    public static function get($name, $default = false)
    {
        self::$readTimes++;

        return self::init()->get($name, $default);
    }

    /**
     * 寫入快取
     * @access public
     * @param  string   $name   快取標識
     * @param  mixed    $value  存储資料
     * @param  int|null $expire 有效時間 0為永久
     * @return boolean
     */
    public static function set($name, $value, $expire = null)
    {
        self::$writeTimes++;

        return self::init()->set($name, $value, $expire);
    }

    /**
     * 自增快取（針對數值快取）
     * @access public
     * @param  string $name 快取变量名
     * @param  int    $step 步長
     * @return false|int
     */
    public static function inc($name, $step = 1)
    {
        self::$writeTimes++;

        return self::init()->inc($name, $step);
    }

    /**
     * 自减快取（針對數值快取）
     * @access public
     * @param  string $name 快取变量名
     * @param  int    $step 步長
     * @return false|int
     */
    public static function dec($name, $step = 1)
    {
        self::$writeTimes++;

        return self::init()->dec($name, $step);
    }

    /**
     * 刪除快取
     * @access public
     * @param  string $name 快取標識
     * @return boolean
     */
    public static function rm($name)
    {
        self::$writeTimes++;

        return self::init()->rm($name);
    }

    /**
     * 清除快取
     * @access public
     * @param  string $tag 标签名
     * @return boolean
     */
    public static function clear($tag = null)
    {
        self::$writeTimes++;

        return self::init()->clear($tag);
    }

    /**
     * 读取快取並刪除
     * @access public
     * @param  string $name 快取变量名
     * @return mixed
     */
    public static function pull($name)
    {
        self::$readTimes++;
        self::$writeTimes++;

        return self::init()->pull($name);
    }

    /**
     * 如果不存在則寫入快取
     * @access public
     * @param  string $name   快取变量名
     * @param  mixed  $value  存储資料
     * @param  int    $expire 有效時間 0為永久
     * @return mixed
     */
    public static function remember($name, $value, $expire = null)
    {
        self::$readTimes++;

        return self::init()->remember($name, $value, $expire);
    }

    /**
     * 快取标签
     * @access public
     * @param  string       $name    标签名
     * @param  string|array $keys    快取標識
     * @param  bool         $overlay 是否覆盖
     * @return Driver
     */
    public static function tag($name, $keys = null, $overlay = false)
    {
        return self::init()->tag($name, $keys, $overlay);
    }

}
