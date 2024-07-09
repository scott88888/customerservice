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

use think\exception\ClassNotFoundException;

/**
 * Class Log
 * @package think
 *
 * @method void log($msg) static 记录一般日誌
 * @method void error($msg) static 记录錯誤日誌
 * @method void info($msg) static 记录一般訊息日誌
 * @method void sql($msg) static 记录 SQL 查詢日誌
 * @method void notice($msg) static 记录提示日誌
 * @method void alert($msg) static 记录报警日誌
 */
class Log
{
    const LOG    = 'log';
    const ERROR  = 'error';
    const INFO   = 'info';
    const SQL    = 'sql';
    const NOTICE = 'notice';
    const ALERT  = 'alert';
    const DEBUG  = 'debug';

    /**
     * @var array 日誌訊息
     */
    protected static $log = [];

    /**
     * @var array 配置参數
     */
    protected static $config = [];

    /**
     * @var array 日誌類型
     */
    protected static $type = ['log', 'error', 'info', 'sql', 'notice', 'alert', 'debug'];

    /**
     * @var log\driver\File|log\driver\Test|log\driver\Socket 日誌寫入驱動
     */
    protected static $driver;

    /**
     * @var string 當前日誌授权 key
     */
    protected static $key;

    /**
     * 日誌初始化
     * @access public
     * @param  array $config 配置参數
     * @return void
     */
    public static function init($config = [])
    {
        $type  = isset($config['type']) ? $config['type'] : 'File';
        $class = false !== strpos($type, '\\') ? $type : '\\think\\log\\driver\\' . ucwords($type);

        self::$config = $config;
        unset($config['type']);

        if (class_exists($class)) {
            self::$driver = new $class($config);
        } else {
            throw new ClassNotFoundException('class not exists:' . $class, $class);
        }

        // 记录初始化訊息
        App::$debug && Log::record('[ LOG ] INIT ' . $type, 'info');
    }

    /**
     * 取得日誌訊息
     * @access public
     * @param  string $type 訊息類型
     * @return array|string
     */
    public static function getLog($type = '')
    {
        return $type ? self::$log[$type] : self::$log;
    }

    /**
     * 记录调试訊息
     * @access public
     * @param  mixed  $msg  调试訊息
     * @param  string $type 訊息類型
     * @return void
     */
    public static function record($msg, $type = 'log')
    {
        self::$log[$type][] = $msg;

        // 命令行下面日誌寫入改进
        IS_CLI && self::save();
    }

    /**
     * 清空日誌訊息
     * @access public
     * @return void
     */
    public static function clear()
    {
        self::$log = [];
    }

    /**
     * 設定當前日誌记录的授权 key
     * @access public
     * @param  string $key 授权 key
     * @return void
     */
    public static function key($key)
    {
        self::$key = $key;
    }

    /**
     * 檢查日誌寫入权限
     * @access public
     * @param  array $config 當前日誌配置参數
     * @return bool
     */
    public static function check($config)
    {
        return !self::$key || empty($config['allow_key']) || in_array(self::$key, $config['allow_key']);
    }

    /**
     * 保存调试訊息
     * @access public
     * @return bool
     */
    public static function save()
    {
        // 没有需要保存的记录則直接返回
        if (empty(self::$log)) {
            return true;
        }

        is_null(self::$driver) && self::init(Config::get('log'));

        // 检测日誌寫入权限
        if (!self::check(self::$config)) {
            return false;
        }

        if (empty(self::$config['level'])) {
            // 取得全部日誌
            $log = self::$log;
            if (!App::$debug && isset($log['debug'])) {
                unset($log['debug']);
            }
        } else {
            // 记录允许级别
            $log = [];
            foreach (self::$config['level'] as $level) {
                if (isset(self::$log[$level])) {
                    $log[$level] = self::$log[$level];
                }
            }
        }

        if ($result = self::$driver->save($log, true)) {
            self::$log = [];
        }

        Hook::listen('log_write_done', $log);

        return $result;
    }

    /**
     * 实时寫入日誌訊息 並支援行為
     * @access public
     * @param  mixed  $msg   调试訊息
     * @param  string $type  訊息類型
     * @param  bool   $force 是否强制寫入
     * @return bool
     */
    public static function write($msg, $type = 'log', $force = false)
    {
        $log = self::$log;

        // 如果不是强制寫入，而且訊息類型不在可记录的類别中則直接返回 false 不做记录
        if (true !== $force && !empty(self::$config['level']) && !in_array($type, self::$config['level'])) {
            return false;
        }

        // 封装日誌訊息
        $log[$type][] = $msg;

        // 监听 log_write
        Hook::listen('log_write', $log);

        is_null(self::$driver) && self::init(Config::get('log'));

        // 寫入日誌
        if ($result = self::$driver->save($log, false)) {
            self::$log = [];
        }

        return $result;
    }

    /**
     * 静态方法调用
     * @access public
     * @param  string $method 调用方法
     * @param  mixed  $args   参數
     * @return void
     */
    public static function __callStatic($method, $args)
    {
        if (in_array($method, self::$type)) {
            array_push($args, $method);

            call_user_func_array('\\think\\Log::record', $args);
        }
    }

}
