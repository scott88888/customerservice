<?php


namespace app\admin\validate;

use think\Validate;

/**
 *  註冊驗證器.
 */
class Admins extends Validate
{

    /**
     * 驗證規則.
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
     * 驗證失敗訊息.
     * [$message description]
     * @var array
     */
    protected $message = [
        "username.require" => "請填寫使用者名稱称",
        "username.unique"  => "該使用者名稱存在",
        "username.alphaNum" => "使用者名稱只能是字母和數字",
        "username.length" => "使用者名稱長度為1~16个字符",
        "nickname.length" => "暱稱長度為2~20个字符",
        "password.requireIf" => "請填寫登入密碼",
        "password.length" => "登入密碼長度為6~16个字符",
        "password2.confirm" => "密碼不一致",
        "password2.require" => "請再次输入密碼",
        'captcha.require' => '請填寫驗證碼',
        'captcha.captcha' => '驗證碼不正确',
        "email.email" => "格式不符合要求",

    ];


    /**
     * 驗證场景.
     * @access protected
     * @var array
     */
    protected $scene = [

    ];

}
