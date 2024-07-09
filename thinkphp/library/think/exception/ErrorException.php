<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://zjzit.cn>
// +----------------------------------------------------------------------

namespace think\exception;

use think\Exception;

/**
 * ThinkPHP錯誤异常
 * 主要用于封装 set_error_handler 和 register_shutdown_function 得到的錯誤
 * 除開从 think\Exception 继承的功能
 * 其他和PHP系统\ErrorException功能基本一样
 */
class ErrorException extends Exception
{
    /**
     * 用于保存錯誤级别
     * @var integer
     */
    protected $severity;

    /**
     * 錯誤异常构造函數
     * @param integer $severity 錯誤级别
     * @param string  $message  錯誤详细訊息
     * @param string  $file     出错文件路徑
     * @param integer $line     出错行号
     * @param array   $context  錯誤上下文，会包含錯誤触发处作用域内所有变量的數组
     */
    public function __construct($severity, $message, $file, $line, array $context = [])
    {
        $this->severity = $severity;
        $this->message  = $message;
        $this->file     = $file;
        $this->line     = $line;
        $this->code     = 0;

        empty($context) || $this->setData('Error Context', $context);
    }

    /**
     * 取得錯誤级别
     * @return integer 錯誤级别
     */
    final public function getSeverity()
    {
        return $this->severity;
    }
}
