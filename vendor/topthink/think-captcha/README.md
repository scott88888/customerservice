# think-captcha
thinkphp5 驗證碼类库

## 安装
> composer require topthink/think-captcha


##使用

###模板里输出驗證碼

~~~
<div>{:captcha_img()}</div>
~~~
或者
~~~
<div><img src="{:captcha_src()}" alt="captcha" /></div>
~~~
> 上面两种的最终效果是一样的

### 控制器里驗證
使用TP5的内置驗證功能即可
~~~
$this->validate($data,[
    'captcha|驗證碼'=>'required|captcha'
]);
~~~
或者手动驗證
~~~
if(!captcha_check($captcha)){
 //驗證失敗
};
~~~