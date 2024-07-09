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

use think\db\Connection;
use think\db\Query;

/**
 * Class Db
 * @package think
 * @method Query table(string $table) static 指定資料表（含前缀）
 * @method Query name(string $name) static 指定資料表（不含前缀）
 * @method Query where(mixed $field, string $op = null, mixed $condition = null) static 查詢條件
 * @method Query join(mixed $join, mixed $condition = null, string $type = 'INNER') static JOIN查詢
 * @method Query union(mixed $union, boolean $all = false) static UNION查詢
 * @method Query limit(mixed $offset, integer $length = null) static 查詢LIMIT
 * @method Query order(mixed $field, string $order = null) static 查詢ORDER
 * @method Query cache(mixed $key = null , integer $expire = null) static 設定查詢缓存
 * @method mixed value(string $field) static 取得某个字段的值
 * @method array column(string $field, string $key = '') static 取得某个列的值
 * @method Query view(mixed $join, mixed $field = null, mixed $on = null, string $type = 'INNER') static 视图查詢
 * @method mixed find(mixed $data = null) static 查詢單个记录
 * @method mixed select(mixed $data = null) static 查詢多个记录
 * @method integer insert(array $data, boolean $replace = false, boolean $getLastInsID = false, string $sequence = null) static 插入一條记录
 * @method integer insertGetId(array $data, boolean $replace = false, string $sequence = null) static 插入一條记录并返回自增ID
 * @method integer insertAll(array $dataSet) static 插入多條记录
 * @method integer update(array $data) static 更新记录
 * @method integer delete(mixed $data = null) static 刪除记录
 * @method boolean chunk(integer $count, callable $callback, string $column = null) static 分块取得資料
 * @method mixed query(string $sql, array $bind = [], boolean $master = false, bool $pdo = false) static SQL查詢
 * @method integer execute(string $sql, array $bind = [], boolean $fetch = false, boolean $getLastInsID = false, string $sequence = null) static SQL执行
 * @method Paginator paginate(integer $listRows = 15, mixed $simple = null, array $config = []) static 分頁查詢
 * @method mixed transaction(callable $callback) static 执行資料库事务
 * @method void startTrans() static 启動事务
 * @method void commit() static 用于非自動送出狀態下面的查詢送出
 * @method void rollback() static 事务回滚
 * @method boolean batchQuery(array $sqlArray) static 批处理执行SQL语句
 * @method string quote(string $str) static SQL指令安全过滤
 * @method string getLastInsID($sequence = null) static 取得最近插入的ID
 */
class Db
{
    /**
     * @var Connection[] 資料库連結实例
     */
    private static $instance = [];

    /**
     * @var int 查詢次數
     */
    public static $queryTimes = 0;

    /**
     * @var int 执行次數
     */
    public static $executeTimes = 0;

    /**
     * 資料库初始化，并取得資料库类实例
     * @access public
     * @param  mixed       $config 連結配置
     * @param  bool|string $name   連結標識 true 强制重新連結
     * @return Connection
     * @throws Exception
     */
    public static function connect($config = [], $name = false)
    {
        if (false === $name) {
            $name = md5(serialize($config));
        }

        if (true === $name || !isset(self::$instance[$name])) {
            // 解析連結参數 支持數组和字符串
            $options = self::parseConfig($config);

            if (empty($options['type'])) {
                throw new \InvalidArgumentException('Undefined db type');
            }

            $class = false !== strpos($options['type'], '\\') ?
            $options['type'] :
            '\\think\\db\\connector\\' . ucwords($options['type']);

            // 记录初始化訊息
            if (App::$debug) {
                Log::record('[ DB ] INIT ' . $options['type'], 'info');
            }

            if (true === $name) {
                $name = md5(serialize($config));
            }

            self::$instance[$name] = new $class($options);
        }

        return self::$instance[$name];
    }

    /**
     * 清除連結实例
     * @access public
     * @return void
     */
    public static function clear()
    {
        self::$instance = [];
    }

    /**
     * 資料库連結参數解析
     * @access private
     * @param  mixed $config 連結参數
     * @return array
     */
    private static function parseConfig($config)
    {
        if (empty($config)) {
            $config = Config::get('database');
        } elseif (is_string($config) && false === strpos($config, '/')) {
            $config = Config::get($config); // 支持读取配置参數
        }

        return is_string($config) ? self::parseDsn($config) : $config;
    }

    /**
     * DSN 解析
     * 格式： mysql://username:passwd@localhost:3306/DbName?param1=val1&param2=val2#utf8
     * @access private
     * @param  string $dsnStr 資料库 DSN 字符串解析
     * @return array
     */
    private static function parseDsn($dsnStr)
    {
        $info = parse_url($dsnStr);

        if (!$info) {
            return [];
        }

        $dsn = [
            'type'     => $info['scheme'],
            'username' => isset($info['user']) ? $info['user'] : '',
            'password' => isset($info['pass']) ? $info['pass'] : '',
            'hostname' => isset($info['host']) ? $info['host'] : '',
            'hostport' => isset($info['port']) ? $info['port'] : '',
            'database' => !empty($info['path']) ? ltrim($info['path'], '/') : '',
            'charset'  => isset($info['fragment']) ? $info['fragment'] : 'utf8',
        ];

        if (isset($info['query'])) {
            parse_str($info['query'], $dsn['params']);
        } else {
            $dsn['params'] = [];
        }

        return $dsn;
    }

    /**
     * 调用驱動类的方法
     * @access public
     * @param  string $method 方法名
     * @param  array  $params 参數
     * @return mixed
     */
    public static function __callStatic($method, $params)
    {
        return call_user_func_array([self::connect(), $method], $params);
    }
}
