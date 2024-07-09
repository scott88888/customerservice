<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 應用入口文件 ]

ini_set('session.gc_maxlifetime', 432000);
ini_set('session.cookie_lifetime', 432000);
ini_set('session.gc_probability',1);
ini_set('session.gc_divisor',1000);


isset($_SESSION) or session_start();

// 定義环境版本

// 定義應用目錄
define('APP_PATH', __DIR__ . '/../application/');
define('VENDOR',__DIR__.'/../vendor/');

// 定義配置文件目錄
define('CONF_PATH', __DIR__ . '/../config/'); 


// 定義pusher密匙
define('app_key','b7tyzijdqy5hq9qt');
define('app_secret','nnregp2pcxfun0j0lxdbv06tjz0bbcn5');
define('app_id',232);
define('whost','ws://43.198.210.117');
define('ahost','http://43.198.210.117');
define('wport',9090);
define('aport',2080);
define('registToken','41851672');
define('AIKF_SALT','5g3j7hcawccxgfq4k1');
define('AKF_VERSION','AI_KF');

// 自訂一个 入口 目錄
define('PUBLIC_PATH',__DIR__);
// 定義 類的文件路徑
define('EXTEND_PATH','../extend/');

// 定義微信配置
define('appid','');
define('appsecret','');
define('token','');
define('domain','http://43.198.210.117');

// 載入框架引导文件
require __DIR__ . '/../thinkphp/start.php';