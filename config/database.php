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

return [
    // 資料库调试模式
    'debug'          => false,
    // 是否严格檢查字段是否存在
    'fields_strict'  => true,
    // 是否自動写入時間戳字段
    'auto_timestamp' => false,
    // 是否需要进行SQL性能分析
    'sql_explain'    => false,

    // 資料库类型
    'type'           => 'mysql',
    // 服务器地址
    'hostname'       => '127.0.0.1',
    // 資料库名
    'database'       => 'scott',
    // 使用者名稱
    'username'       => 'scott',
    // 密碼
    'password'       => '123456',
    // 端口
    'hostport'       => '',
    // 資料库表前缀
    'prefix'         => '',
    // 資料库编碼默认采用utf8
    'charset'        => 'utf8',
    // 資料库連結参數
    'params'         => [],
];
