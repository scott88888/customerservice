<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\controller;

use think\App;
use think\Request;
use think\Response;

abstract class Rest
{

    protected $method; // 當前請求類型
    protected $type; // 當前資源類型
    // 输出類型
    protected $restMethodList    = 'get|post|put|delete';
    protected $restDefaultMethod = 'get';
    protected $restTypeList      = 'html|xml|json|rss';
    protected $restDefaultType   = 'html';
    protected $restOutputType    = [ // REST允许输出的資源類型列表
        'xml'  => 'application/xml',
        'json' => 'application/json',
        'html' => 'text/html',
    ];

    /**
     * 构造函數 取得模板對象實例
     * @access public
     */
    public function __construct()
    {
        // 資源類型检测
        $request = Request::instance();
        $ext     = $request->ext();
        if ('' == $ext) {
            // 自動检测資源類型
            $this->type = $request->type();
        } elseif (!preg_match('/(' . $this->restTypeList . ')$/i', $ext)) {
            // 資源類型非法 則用默認資源類型訪問
            $this->type = $this->restDefaultType;
        } else {
            $this->type = $ext;
        }
        // 請求方式检测
        $method = strtolower($request->method());
        if (!preg_match('/(' . $this->restMethodList . ')$/i', $method)) {
            // 請求方式非法 則用默認請求方法
            $method = $this->restDefaultMethod;
        }
        $this->method = $method;
    }

    /**
     * REST 调用
     * @access public
     * @param string $method 方法名
     * @return mixed
     * @throws \Exception
     */
    public function _empty($method)
    {
        if (method_exists($this, $method . '_' . $this->method . '_' . $this->type)) {
            // RESTFul方法支援
            $fun = $method . '_' . $this->method . '_' . $this->type;
        } elseif ($this->method == $this->restDefaultMethod && method_exists($this, $method . '_' . $this->type)) {
            $fun = $method . '_' . $this->type;
        } elseif ($this->type == $this->restDefaultType && method_exists($this, $method . '_' . $this->method)) {
            $fun = $method . '_' . $this->method;
        }
        if (isset($fun)) {
            return App::invokeMethod([$this, $fun]);
        } else {
            // 抛出异常
            throw new \Exception('error action :' . $method);
        }
    }

    /**
     * 输出返回資料
     * @access protected
     * @param mixed     $data 要返回的資料
     * @param String    $type 返回類型 JSON XML
     * @param integer   $code HTTP狀態碼
     * @return Response
     */
    protected function response($data, $type = 'json', $code = 200)
    {
        return Response::create($data, $type)->code($code);
    }

}
