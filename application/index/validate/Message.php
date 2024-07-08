<?php 

namespace app\index\validate;
use think\Validate;


class Message extends Validate{

    /**
    * 驗證规则
    * [$rule description]
    * @var [type]
    */
	protected $rule=[
	    "name"          => 'require',
        "content"       => "require"
	];


	 /**
    * 驗證失敗 信息
    * [$message description]
    * @var array
    */
   protected $message =[
        "username.require"       => "请填写使用者名稱称",
        'content.require'        => '请填写留言',
   ];
  
}



 ?>