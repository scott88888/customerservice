<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2020/1/29
 * Time: 9:41
 */


namespace app\backend\validate;

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
        'username' => 'require',
        'password' => 'require',
        'captcha'  => 'require|captcha:backend_login'
    ];

    protected $field = [
        'username'  => '使用者名稱',
        'password'   => '密碼',
        'captcha' => '驗證碼',
    ];

    /**
     * 驗證消息.
     * [$messege description]
     * @var [type]
     */
    protected $message = [
        'username.require' => '請填写登入帐号',
        'password.require' => '請填写登入密碼',
        'captcha.require'  => '請填写驗證碼',
        'captcha.captcha'  => '驗證碼不正确'
    ];
}
