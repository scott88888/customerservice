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

class Php
{
    // 模板引擎参數
    protected $config = [
        // 视圖基础目錄（集中式）
        'view_base'   => '',
        // 模板起始路徑
        'view_path'   => '',
        // 模板文件後缀
        'view_suffix' => 'php',
        // 模板文件名分隔符
        'view_depr'   => DS,
        // 默認模板渲染規則 1 解析為小寫+下划线 2 全部轉換小寫
        'auto_rule'   => 1,
    ];
    protected $template;
    protected $content;

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
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
     * @return void
     */
    public function fetch($template, $data = [])
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
        $this->template = $template;
        // 记录视圖訊息
        App::$debug && Log::record('[ VIEW ] ' . $template . ' [ ' . var_export(array_keys($data), true) . ' ]', 'info');

        extract($data, EXTR_OVERWRITE);
        include $this->template;
    }

    /**
     * 渲染模板内容
     * @access public
     * @param string    $content 模板内容
     * @param array     $data 模板变量
     * @return void
     */
    public function display($content, $data = [])
    {
        $this->content = $content;

        extract($data, EXTR_OVERWRITE);
        eval('?>' . $this->content);
    }

    /**
     * 自動定位模板文件
     * @access private
     * @param string $template 模板文件規則
     * @return string
     */
    private function parseTemplate($template)
    {
        if (empty($this->config['view_path'])) {
            $this->config['view_path'] = App::$modulePath . 'view' . DS;
        }

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
     * 配置模板引擎
     * @access private
     * @param string|array  $name 参數名
     * @param mixed         $value 参數值
     * @return void
     */
    public function config($name, $value = null)
    {
        if (is_array($name)) {
            $this->config = array_merge($this->config, $name);
        } elseif (is_null($value)) {
            return isset($this->config[$name]) ? $this->config[$name] : null;
        } else {
            $this->config[$name] = $value;
        }
    }

}
