ThinkPHP 5.0 MongoDb驱動
===============

首先安装官方的mongodb扩展：

http://pecl.php.net/package/mongodb

然後，配置應用的資料庫配置文件`database.php`的`type`参數為：

~~~
'type'  =>  '\think\mongo\Connection',
~~~

即可正常使用MongoDb，例如：
~~~
Db::name('demo')
    ->find();
Db::name('demo')
    ->field('id,name')
    ->limit(10)
    ->order('id','desc')
    ->select();
~~~
