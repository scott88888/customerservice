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

namespace think\view\driver;

use think\App;
use think\exception\TemplateNotFoundException;
use think\Loader;
use think\Log;
use think\Request;
use think\Template;

class Think
{
    // 模板引擎實例
    private $template;
    // 模板引擎参數
    protected $config = [
        // 视圖基础目錄（集中式）
        'view_base'   => '',
        // 模板起始路徑
        'view_path'   => '',
        // 模板文件後缀
        'view_suffix' => 'html',
        // 模板文件名分隔符
        'view_depr'   => DS,
        // 是否開啟模板编译快取,设為false則每次都会重新编译
        'tpl_cache'   => true,
        // 默認模板渲染規則 1 解析為小寫+下划线 2 全部轉換小寫
        'auto_rule'   => 1,
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
        if (empty($this->config['view_path'])) {
            $this->config['view_path'] = App::$modulePath . 'view' . DS;
        }

        $this->template = new Template($this->config);
    }

    /**
     * 检测是否存在模板文件
     * @access public
     * @param string $template 模板文件或者模板規則
     * @return bool
     */
    public function exists($template)
    {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 取得模板文件名
            $template = $this->parseTemplate($template);
        }
        return is_file($template);
    }

    /**
     * 渲染模板文件
     * @access public
     * @param string    $template 模板文件
     * @param array     $data 模板变量
     * @param array     $config 模板参數
     * @return void
     */
    public function fetch($template, $data = [], $config = [])
    {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 取得模板文件名
            $template = $this->parseTemplate($template);
        }
        // 模板不存在 抛出异常
        if (!is_file($template)) {
            header("Location: /service/index/index");
//            throw new TemplateNotFoundException('template not exists:' . $template, $template);
        }
        // 记录视圖訊息
        App::$debug && Log::record('[ VIEW ] ' . $template . ' [ ' . var_export(array_keys($data), true) . ' ]', 'info');
        $this->template->fetch($template, $data, $config);
    }

    /**
     * 渲染模板内容
     * @access public
     * @param string    $template 模板内容
     * @param array     $data 模板变量
     * @param array     $config 模板参數
     * @return void
     */
    public function display($template, $data = [], $config = [])
    {
        $this->template->display($template, $data, $config);
    }

    /**
     * 自動定位模板文件
     * @access private
     * @param string $template 模板文件規則
     * @return string
     */
    private function parseTemplate($template)
    {
        // 分析模板文件規則
        $request = Request::instance();
        // 取得视圖根目錄
        if (strpos($template, '@')) {
            // 跨模組调用
            list($module, $template) = explode('@', $template);
        }
        if ($this->config['view_base']) {
            // 基础视圖目錄
            $module = isset($module) ? $module : $request->module();
            $path   = $this->config['view_base'] . ($module ? $module . DS : '');
        } else {
            $path = isset($module) ? APP_PATH . $module . DS . 'view' . DS : $this->config['view_path'];
        }

        $depr = $this->config['view_depr'];
        if (0 !== strpos($template, '/')) {
            $template   = str_replace(['/', ':'], $depr, $template);
            $controller = Loader::parseName($request->controller());
            if ($controller) {
                if ('' == $template) {
                    // 如果模板文件名為空 按照默認規則定位
                    $template = str_replace('.', DS, $controller) . $depr . (1 == $this->config['auto_rule'] ? Loader::parseName($request->action(true)) : $request->action());
                } elseif (false === strpos($template, $depr)) {
                    $template = str_replace('.', DS, $controller) . $depr . $template;
                }
            }
        } else {
            $template = str_replace(['/', ':'], $depr, substr($template, 1));
        }
        return $path . ltrim($template, '/') . '.' . ltrim($this->config['view_suffix'], '.');
    }

    /**
     * 配置或者取得模板引擎参數
     * @access private
     * @param string|array  $name 参數名
     * @param mixed         $value 参數值
     * @return mixed
     */
    public function config($name, $value = null)
    {
        if (is_array($name)) {
            $this->template->config($name);
            $this->config = array_merge($this->config, $name);
        } elseif (is_null($value)) {
            return $this->template->config($name);
        } else {
            $this->template->$name = $value;
            $this->config[$name]   = $value;
        }
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->template, $method], $params);
    }
}
