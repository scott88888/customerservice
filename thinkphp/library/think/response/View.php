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

namespace think\response;

use think\Config;
use think\Response;
use think\View as ViewTemplate;

class View extends Response
{
    // 输出参數
    protected $options     = [];
    protected $vars        = [];
    protected $replace     = [];
    protected $contentType = 'text/html';

    /**
     * 處理資料
     * @access protected
     * @param mixed $data 要處理的資料
     * @return mixed
     */
    protected function output($data)
    {
        // 渲染模板输出
        return ViewTemplate::instance(Config::get('template'), Config::get('view_replace_str'))
            ->fetch($data, $this->vars, $this->replace);
    }

    /**
     * 取得视圖变量
     * @access public
     * @param string $name 模板变量
     * @return mixed
     */
    public function getVars($name = null)
    {
        if (is_null($name)) {
            return $this->vars;
        } else {
            return isset($this->vars[$name]) ? $this->vars[$name] : null;
        }
    }

    /**
     * 模板变量赋值
     * @access public
     * @param mixed $name  变量名
     * @param mixed $value 变量值
     * @return $this
     */
    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->vars = array_merge($this->vars, $name);
            return $this;
        } else {
            $this->vars[$name] = $value;
        }
        return $this;
    }

    /**
     * 视圖内容替换
     * @access public
     * @param string|array $content 被替换内容（支援批量替换）
     * @param string  $replace    替换内容
     * @return $this
     */
    public function replace($content, $replace = '')
    {
        if (is_array($content)) {
            $this->replace = array_merge($this->replace, $content);
        } else {
            $this->replace[$content] = $replace;
        }
        return $this;
    }

}
