<?php 

namespace app\index\validate;
use think\Validate;


class Message extends Validate{

    /**
    * 驗證規則
    * [$rule description]
    * @var [type]
    */
	protected $rule=[
	    "name"          => 'require',
        "content"       => "require"
	];


	 /**
    * 驗證失敗 訊息
    * [$message description]
    * @var array
    */
   protected $message =[
        "username.require"       => "請填寫使用者名稱称",
        'content.require'        => '請填寫留言',
   ];
  
}



 ?>