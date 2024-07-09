<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2020/1/14
 * Time: 9:44
 */

namespace app\api\controller;

use app\common\exception\ApiException;
use think\Controller;

class Base extends Controller
{
    /**
     * 初始化的方法
     */
    public function _initialize() {
        $this->checkRequestAuth();
    }

    /**
     * 檢查每次app請求的資料是否合法
     */
    public function checkRequestAuth() {
        // 首先需要取得headers
        $headers = request()->header();

        // 基础参數校验
//        if(empty($headers['sign'])) {
//            throw new ApiException([
//                'msg' => 'sign不正确',
//                'errorCode' => 10001
//            ]);
//        }
//        // 需要sign
//        if(!IAuth::checkSignPass($headers)) {
//            throw new ApiException([
//                'msg'=>'sign驗證失敗',
//                'errorCode'=>10003
//            ]);
//        }
        // 1、文件  2、mysql 3、redis
        $this->headers = $headers;
    }
}