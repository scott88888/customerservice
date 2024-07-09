<?php
use think\Db;
if (!function_exists('db')) {
    /**
     * 实例化資料库类
     * @param string        $name 操作的資料表名稱（不含前缀）
     * @param array|string  $config 資料库配置参数
     * @param bool          $force 是否强制重新连接
     * @return \think\db\Query
     */
    function db($name = '', $config = [], $force = false)
    {
        return Db::connect($config, $force)->name($name);
    }
}