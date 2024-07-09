<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2020/1/29
 * Time: 9:41
 */

namespace app\platform\validate;

use think\Validate;

class Admin extends Validate
{

    /**
     * 驗證规则.
     * [$rule description]
     * @var [type]
     */
    protected $rule = [
        "username" => "require|length:3,16|alphaDash",
        "password" => "require|length:6,16",
        "old_password" => "require|length:6,16",
        "new_password" => "require|length:6,16",
        "app_max_count" => "require|number",
        "mobile" => "unique:wolive_admin|isMobile",
        "mobile2" => "isMobile",
        'expire_time' => 'isDate|afterTime',
        'captcha_code'  => 'require|captcha:resetpasswd'
    ];

    protected $field = [
        'mobile2'  => '手機號碼',
        'captcha_code' => '驗證碼',
    ];
    /**
     * 驗證失敗訊息.
     * [$message description]
     * @var array
     */
    protected $message = [
        "username.require" => "請填写使用者名稱",
        "username.alphaDash" => "使用者名稱只能是字母、數字和下划线_及破折号-",
        "username.length" => "使用者名稱長度為3~16个字符",
        "password.require" => "密碼不能為空",
        "password.length" => "密碼長度為6~16个字符",
        "mobile.isMobile" => "手机格式不符合要求",
        "mobile2.isMobile" => "手机格式不符合要求",
        "mobile.unique" => "该手機號碼已存在",
        'expire_time.isDate' => '有效期格式不正确',
        'expire_time.afterTime' => '無法建立一个过期的应用',
        "app_max_count.require" => "請填写數量",
        "app_max_count.number" => "应用數量只能是數字",
    ];


    /**
     * 驗證场景.
     * @access protected
     * @var array
     */
    protected $scene = [
        'edit'  =>  ['mobile'],
        'insert' => ['username','password','mobile','app_max_count','expire_time'],
        'changepwd' => ['old_password','new_password'],
        'resetpwd' => ['captcha_code','mobile2'],
        'changeusrpwd' => ['username','password']
    ];

    protected function isDate($value)
    {
        // 是否是一个有效日期
        if ($value == 0 ) {
            return true;
        }
        if (strtotime(date('Y-m-d H:i:s',$value)) != $value) {
            return false;
        }
        $result = false !== strtotime(date('Y-m-d H:i:s',$value));
        return $result;
    }

    protected function afterTime($value)
    {
        if ($value == 0) {
            return true;
        }
        return $value >= strtotime(date('Y-m-d',time()));
    }

    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}