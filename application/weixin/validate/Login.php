<?php
/**
 * Created by PhpStorm.
 * User: chenrui
 * Date: 2017/8/7
 * Time: 17:12
 */

namespace app\weixin\validate;
use think\Validate;

class Login extends Validate
{
   protected $rule=[
       'username' => 'require',
       'password' => 'require',
   ];

    protected $messege = [
        'username.require' => '請填写登入帐号',
        'password.require' => '請填写登入密碼'
    ];
}