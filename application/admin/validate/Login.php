<?php


namespace app\admin\validate;

use think\Validate;

/**
 *
 * 登入驗證器.
 */
class Login extends Validate
{

    /**
     * 驗證规则.
     * [$rule description]
     * @var array
     */
    protected $rule = [
        'user_name' => 'require',
        'password' => 'require',
        'captcha'  => 'require|captcha:admin_login'
    ];

    /**
     * 驗證消息.
     * [$messege description]
     * @var [type]
     */
    protected $messege = [
        'username.require' => '請填写登入帐号',
        'password.require' => '請填写登入密碼',
        'captcha.require'  => '請填写驗證碼',
        'captcha.captcha'  => '驗證碼不正确'
    ];
}
