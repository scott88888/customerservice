<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2020/1/30
 * Time: 17:12
 */

namespace app\platform\validate;

use think\Validate;

class App extends Validate
{

    /**
     * 驗證規則.
     * [$rule description]
     * @var array
     */
    protected $rule = [
        'business_name' => 'require|length:3,16|chsDash',
        'user_name' => 'require|length:3,16|alphaDash',
        'password'  => 'require|length:6,16',
        'max_count' => 'require|number',
        'expire_time' => 'date'
    ];

    /**
     * 驗證消息.
     * [$messege description]
     * @var [type]
     */
    protected $message = [
        'business_name.require' => '請填寫客服系统名稱',
        'business_name.length' => '客服系统名稱為3~16个字符',
        'business_name.chsDash' => '客服系统名稱只能是汉字、字母、數字和下划线_及破折号-',
        'user_name.require' => '請填寫帐号',
        'user_name.length' => '管理员账号為3~16个字符',
        'user_name.alphaDash' => '管理员账号只能是字母、數字、下划线 _ ',
        'password.require' => '請填寫登入密碼',
        'password.length' => '密碼長度為1~16个字符',
        'max_count.require' =>'請填寫數量',
        'max_count.number' =>'客服數量只能是數字',
        'expire_time' => '有效期格式不正确',
    ];

    protected $scene = [
        'edit' => ['business_name','max_count'],
        'insert' => ['business_name','user_name','password','max_count']
    ];
}