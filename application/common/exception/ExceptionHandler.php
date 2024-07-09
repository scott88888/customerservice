<?php
/**
 * Created by PhpStorm.
 * User: 七月
 * Date: 2017/2/12
 * Time: 19:44
 */

namespace app\common\exception;


use think\exception\Handle;
use think\Log;
use think\Request;
use Exception;

/*
 * 这里是重寫Handle的render方法，實現自訂异常消息
 */

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    public function render(Exception $e)
    {
        if ($e instanceof BaseException) {
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        } else {
            if (config('app_debug')) {
                // 调试狀態下需要显示TP默認的异常頁面，因為TP的默認頁面
                // 很容易看出問題
                return parent::render($e);
            }

            $this->code = 500;
            $this->msg = 'sorry，we make a mistake. (^o^)Y';
            $this->errorCode = 999;
            $this->recordErrorLog($e);
        }


        $request = Request::instance();
        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => $request = $request->url()
        ];
        return json($result, $this->code);
    }

    /*
     * 将异常寫入日誌
     */
    private function recordErrorLog(Exception $e)
    {
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error']
        ]);
        Log::record($e->getMessage(), 'error');
    }
}