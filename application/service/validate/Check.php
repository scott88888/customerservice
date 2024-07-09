<?php


namespace app\service\validate;

use think\Validate;

/**
 *
 * 登入驗證器.
 */
class Check extends Validate
{

    /**
     * 驗證規則.
     * [$rule description]
     * @var array
     */
    protected $rule = [
        'oldpass'   => 'require',
        'newpass'   => 'require|length:6,16"',
        'newpass2'  => 'require|confirm:newpass'
    ];

    /**
     * 驗證消息.
     * [$messege description]
     * @var [type]
     */
    protected $message = [
        'oldpass.require' => '請填寫旧密碼',
        'newpass.require' => '請填寫新密碼',
        "newpass.length" => "密碼長度為6~16个字符",
        'newpass2.require' => '請再次填寫新密碼',
        "newpass2.confirm" => "新密碼不一致",
    ];

    protected $scene = [
        'change_service_pwd' => ['newpass'],
    ];
}
