<?php


namespace app\admin\validate;

use think\Validate;

/**
 *
 * 登入驗證器.
 */
class Check extends Validate
{

    /**
     * 驗證规则.
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
        'oldpass.require' => '請填写旧密碼',
        'newpass.require' => '請填写新密碼',
        "newpass.length" => "密碼長度為6~16个字符",
        'newpass2.require' => '請再次填写新密碼',
        "newpass2.confirm" => "新密碼不一致",
    ];

    protected $scene = [
        'change_service_pwd' => ['newpass'],
    ];
}
