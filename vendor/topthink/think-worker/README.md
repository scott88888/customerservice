ThinkPHP 5.0 Workerman 扩展
===============

## 安装
composer require topthink/think-worker

## 使用方法
首先建立控制器類並继承 think\worker\Server，然後設定属性和新增回调方法

~~~
namespace app\index\controller;

use think\worker\Server;

class Worker extends Server
{
	protected $socket = 'http://0.0.0.0:2346';

	public function onMessage($connection,$data)
	{
		$connection->send(json_encode($data));
	}
}
~~~
支援workerman所有的回调方法定義（回调方法必须是public類型）


在應用根目錄增加入口文件 server.php

~~~
#!/usr/bin/env php
<?php
define('APP_PATH', __DIR__ . '/application/');

define('BIND_MODULE','index/Worker');

// 載入框架引导文件
require __DIR__ . '/thinkphp/start.php';
~~~

在命令行启動服务端
~~~
php server.php start
~~~


linux下面可以支援下面指令
~~~
php server.php start|stop|status|restart|reload
~~~

在浏览器中进行客户端测试
http://127.0.0.1:2346/?id=1