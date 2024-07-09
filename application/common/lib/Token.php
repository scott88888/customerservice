<?php

namespace app\common\lib;

use app\common\lib\token\Driver;
use think\App;
use think\Config;
use think\Log;

/**
 * Token操作类
 */
class Token
{
    /**
     * @var array Token的实例
     */
    public static $instance = [];

    /**
     * @var object 操作句柄
     */
    public static $handler;

    /**
     * 連結Token驱動
     * @access public
     * @param  array $options 配置數组
     * @param  bool|string $name Token連結標識 true 强制重新連結
     * @return Driver
     */
    public static function connect(array $options = [], $name = false)
    {
        $type = !empty($options['type']) ? $options['type'] : 'Mysql';

        if (false === $name) {
            $name = md5(serialize($options));
        }

        if (true === $name || !isset(self::$instance[$name])) {
            $class = false === strpos($type, '\\') ?
                '\\app\\common\\lib\\token\\driver\\' . ucwords($type) :
                $type;

            // 记录初始化訊息
            App::$debug && Log::record('[ TOKEN ] INIT ' . $type, 'info');

            if (true === $name) {
                return new $class($options);
            }

            self::$instance[$name] = new $class($options);
        }

        return self::$instance[$name];
    }

    /**
     * 自動初始化Token
     * @access public
     * @param  array $options 配置數组
     * @return Driver
     */
    public static function init(array $options = [])
    {
        if (is_null(self::$handler)) {
            if (empty($options) && 'complex' == Config::get('token.type')) {
                $default = Config::get('token.default');
                // 取得默认Token配置，并連結
                $options = Config::get('token.' . $default['type']) ?: $default;
            } elseif (empty($options)) {
                $options = Config::get('token');
            }

            self::$handler = self::connect($options);
        }

        return self::$handler;
    }

    /**
     * 判断Token是否可用(check别名)
     * @access public
     * @param  string $token Token標識
     * @return bool
     */
    public static function has($token, $user_id)
    {
        return self::check($token, $user_id);
    }

    /**
     * 判断Token是否可用
     * @param string $token Token標識
     * @return bool
     */
    public static function check($token, $user_id)
    {
        return self::init()->check($token, $user_id);
    }

    /**
     * 读取Token
     * @access public
     * @param  string $token Token標識
     * @param  mixed $default 默认值
     * @return mixed
     */
    public static function get($token, $default = false)
    {
        return self::init()->get($token, $default);
    }

    /**
     * 写入Token
     * @access public
     * @param  string $token Token標識
     * @param  mixed $user_id 存储資料
     * @param  int|null $expire 有效時間 0為永久
     * @return boolean
     */
    public static function set($token, $user_id, $expire = null)
    {
        return self::init()->set($token, $user_id, $expire);
    }

    /**
     * 刪除Token(delete别名)
     * @access public
     * @param  string $token Token標識
     * @return boolean
     */
    public static function rm($token)
    {
        return self::delete($token);
    }

    /**
     * 刪除Token
     * @param string $token 标签名
     * @return bool
     */
    public static function delete($token)
    {
        return self::init()->delete($token);
    }

    /**
     * 清除Token
     * @access public
     * @param  string $token Token标记
     * @return boolean
     */
    public static function clear($user_id = null)
    {
        return self::init()->clear($user_id);
    }

}
