ThinkPHP 5.0 SAE扩展
===============

新增下面的配置参数即可

~~~
'log'=>[
	'type'=> '\think\sae\Log',
]

'template' => [
	'type'	=>	'Think',
	'compile_type'	=> '\think\sae\Template',

]
'cache'=>[
	'type'  =>  '\think\sae\Cache',
]
~~~

資料库配置文件database.php中修改为：
~~~
// 資料库类型
'type'        => 'mysql',
// 服务器地址
'hostname'    => SAE_MYSQL_HOST_M . ',' . SAE_MYSQL_HOST_S,
// 資料库名
'database'    => SAE_MYSQL_DB,
// 使用者名稱
'username'    => SAE_MYSQL_USER,
// 密碼
'password'    => SAE_MYSQL_PASS,
// 端口
'hostport'    => SAE_MYSQL_PORT,
~~~