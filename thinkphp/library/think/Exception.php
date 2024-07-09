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

namespace think;

class Exception extends \Exception
{
    /**
     * @var array 保存异常頁面显示的额外 Debug 資料
     */
    protected $data = [];

    /**
     * 設定异常额外的 Debug 資料
     * 資料将会显示為下面的格式
     *
     * Exception Data
     * --------------------------------------------------
     * Label 1
     *   key1      value1
     *   key2      value2
     * Label 2
     *   key1      value1
     *   key2      value2
     *
     * @access protected
     * @param  string $label 資料分类，用于异常頁面显示
     * @param  array  $data  需要显示的資料，必须為关联數组
     * @return void
     */
    final protected function setData($label, array $data)
    {
        $this->data[$label] = $data;
    }

    /**
     * 取得异常额外 Debug 資料
     * 主要用于输出到异常頁面便于调试
     * @access public
     * @return array
     */
    final public function getData()
    {
        return $this->data;
    }

}
