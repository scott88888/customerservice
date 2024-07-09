<?php
namespace app\common\exception;

use think\Exception;

/**
 * Class BaseException
 * 自訂异常类基类
 */
class BaseException extends Exception
{
    public $code = 400;
    public $msg = 'invalid parameters';
    public $errorCode = 999;
    
    public $shouldToClient = true;

    /**
     * 构造函數，接收一个关联數组
     * @param array $params 关联數组只应包含code、msg和errorCode，且不应该是空值
     */
    public function __construct($params=[])
    {
        if(!is_array($params)){
            return;
        }
        if(array_key_exists('code',$params)){
            $this->code = $params['code'];
        }
        if(array_key_exists('msg',$params)){
            $this->msg = $params['msg'];
        }
        if(array_key_exists('errorCode',$params)){
            $this->errorCode = $params['errorCode'];
        }
    }
}

