<?php


namespace app\admin\validate;

use think\Validate;

/**
 *  注册验证器.
 */
class Admins extends Validate
{

    /**
     * 验证规则.
     * [$rule description]
     * @var [type]
     */
    protected $rule = [
        "user_name" => "require|length:1,16|alphaNum",
        "password" => "require|length:6,16",
        "password2" => "require|confirm:password",
        "nick_name" => "length:2,20",
        "email" => "email",
        'captcha' => 'require|captcha:admin_regist',

    ];

    /**
     * 验证失败信息.
     * [$message description]
     * @var array
     */
    protected $message = [
        "username.require" => "请填写使用者名稱称",
        "username.unique"  => "该使用者名稱存在",
        "username.alphaNum" => "使用者名稱只能是字母和数字",
        "username.length" => "使用者名稱长度为1~16个字符",
        "nickname.length" => "暱稱长度为2~20个字符",
        "password.requireIf" => "请填写登入密碼",
        "password.length" => "登入密碼长度为6~16个字符",
        "password2.confirm" => "密码不一致",
        "password2.require" => "请再次输入密码",
        'captcha.require' => '请填写验证码',
        'captcha.captcha' => '验证码不正确',
        "email.email" => "格式不符合要求",

    ];


    /**
     * 验证场景.
     * @access protected
     * @var array
     */
    protected $scene = [

    ];

}
