# think-addons
The ThinkPHP5 Addons Package

## 安装
> composer require 5ini99/think-addons:dev-master

## 配置
### 公共配置
```
'addons'=>[
    // 是否自動读取取插件钩子配置訊息（默認是關閉）
    'autoload' => false,
    // 当關閉自動取得配置时需要手動配置hooks訊息
    'hooks' => [
	    // 可以定義多个钩子
        'testhook'=>'test' // 键為钩子名稱，用于在業務中自訂钩子處理，值為實現该钩子的插件，
					// 多个插件可以用數组也可以用逗号分割
	]
]
```
或者在application\extra目錄中新建`addons.php`,内容為：
```
<?php
return [
	// 是否自動读取取插件钩子配置訊息（默認是關閉）
    'autoload' => false,
    // 当關閉自動取得配置时需要手動配置hooks訊息
    'hooks' => [
        // 可以定義多个钩子
        'testhook'=>'test' // 键為钩子名稱，用于在業務中自訂钩子處理，值為實現该钩子的插件，
                    // 多个插件可以用數组也可以用逗号分割
    ]
]
```

## 建立插件
> 建立的插件可以在view视圖中使用，也可以在php業務中使用
 
安装完成後訪問系统时会在项目根目錄產生名為`addons`的目錄，在该目錄中建立需要的插件。

下面寫一个例子：

### 建立test插件
> 在addons目錄中建立test目錄

### 建立钩子實現類
> 在test目錄中建立Test.php類文件。注意：類文件首字母需大寫

```
<?php
namespace addons\test;	// 注意命名空間规范

use think\Addons;

/**
 * 插件测试
 * @author byron sampson
 */
class Test extends Addons	// 需继承think\addons\Addons類
{
	// 该插件的基础訊息
    public $info = [
        'name' => 'test',	// 插件標識
        'title' => '插件测试',	// 插件名稱
        'description' => 'thinkph5插件测试',	// 插件简介
        'status' => 0,	// 狀態
        'author' => 'byron sampson',
        'version' => '0.1'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 實現的testhook钩子方法
     * @return mixed
     */
    public function testhook($param)
    {
		// 调用钩子时候的参數訊息
        print_r($param);
		// 當前插件的配置訊息，配置訊息存在當前目錄的config.php文件中，见下方
        print_r($this->getConfig());
		// 可以返回模板，模板文件默認读取的為插件目錄中的文件。模板名不能為空！
        return $this->fetch('info');
    }

}
```

### 建立插件配置文件
> 在test目錄中建立config.php類文件，插件配置文件可以省略。

```
<?php
return [
    'display' => [
        'title' => '是否显示:',
        'type' => 'radio',
        'options' => [
            '1' => '显示',
            '0' => '不显示'
        ],
        'value' => '1'
    ]
];
```

### 建立钩子模板文件
> 在test目錄中建立info.html模板文件，钩子在使用fetch方法时對应的模板文件。

```
<h1>hello tpl</h1>

如果插件中需要有連結或送出資料的業務，可以在插件中建立controller業務文件，
要訪問插件中的controller时使用addon_url產生url連結。
如下：
<a href="{:addon_url('test://Action/link')}">link test</a>
格式為：
test為插件名，Action為controller中的類名，link為controller中的方法
```

### 建立插件的controller文件
> 在test目錄中建立controller目錄，在controller目錄中建立Action.php文件
> controller類的用法与tp5中的controller一致

```
<?php
namespace addons\test\controller;

class Action
{
    public function link()
    {
        echo 'hello link';
    }
}
```
> 如果需要使用view模板則需要继承`\think\addons\Controller`類
> 模板文件所在位置為插件目錄的view中，規則与模組中的view規則一致

```
<?php
namespace addons\test\controller;

use think\addons\Controller;

class Action extends Controller
{
    public function link()
    {
        return $this->fetch();
    }
}
```

## 使用钩子
> 建立好插件後就可以在正常業務中使用该插件中的钩子了
> 使用钩子的时候第二个参數可以省略

### 模板中使用钩子

```
<div>{:hook('testhook', ['id'=>1])}</div>
```

### php業務中使用
> 只要是thinkphp5正常流程中的任意位置均可以使用

```
hook('testhook', ['id'=>1])
```

## 插件目錄结构
### 最终產生的目錄结构為

```
tp5
 - addons
 -- test
 --- controller
 ---- Action.php
 --- view
 ---- action
 ----- link.html
 --- config.php
 --- info.html
 --- Test.php
 - application
 - thinkphp
 - extend
 - vendor
 - public
```
