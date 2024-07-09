<?php
use think\Db;
if (!function_exists('db')) {
    /**
     * 實例化資料庫類
     * @param string        $name 操作的資料表名稱（不含前缀）
     * @param array|string  $config 資料庫配置参數
     * @param bool          $force 是否强制重新連結
     * @return \think\db\Query
     */
    function db($name = '', $config = [], $force = false)
    {
        return Db::connect($config, $force)->name($name);
    }
}