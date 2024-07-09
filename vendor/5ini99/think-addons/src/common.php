<?php
// +----------------------------------------------------------------------
// | thinkphp5 Addons [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.zzstudio.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Byron Sampson <xiaobo.sun@qq.com>
// +----------------------------------------------------------------------

use think\App;
use think\Hook;
use think\Config;
use think\Loader;
use think\Cache;
use think\Route;

// 插件目錄
define('ADDON_PATH', ROOT_PATH . 'addons' . DS);

// 定義路由
Route::any('addons/execute/:route', "\\think\\addons\\Route@execute");

// 如果插件目錄不存在則建立
if (!is_dir(ADDON_PATH)) {
    @mkdir(ADDON_PATH, 0777, true);
}

// 註冊類的根命名空間
Loader::addNamespace('addons', ADDON_PATH);

// 闭包自動识别插件目錄配置
Hook::add('app_init', function () {
    // 取得開关
    $autoload = (bool)Config::get('addons.autoload', false);
    // 非正是返回
    if (!$autoload) {
        return;
    }
    // 当debug时不快取配置
    $config = App::$debug ? [] : Cache::get('addons', []);
    if (empty($config)) {
        // 读取addons的配置
        $config = (array)Config::get('addons');
        // 读取插件目錄及钩子列表
        $base = get_class_methods("\\think\\Addons");
        // 读取插件目錄中的php文件
        foreach (glob(ADDON_PATH . '*/*.php') as $addons_file) {
            // 格式化路徑訊息
            $info = pathinfo($addons_file);
            // 取得插件目錄名
            $name = pathinfo($info['dirname'], PATHINFO_FILENAME);
            // 找到插件入口文件
            if (strtolower($info['filename']) == strtolower($name)) {
                // 读取出所有公共方法
                $methods = (array)get_class_methods("\\addons\\" . $name . "\\" . $info['filename']);
                // 跟插件基類方法做比對，得到差异结果
                $hooks = array_diff($methods, $base);
                // 循环将钩子方法寫入配置中
                foreach ($hooks as $hook) {
                    if (!isset($config['hooks'][$hook])) {
                        $config['hooks'][$hook] = [];
                    }
                    // 兼容手動配置项
                    if (is_string($config['hooks'][$hook])) {
                        $config['hooks'][$hook] = explode(',', $config['hooks'][$hook]);
                    }
                    if (!in_array($name, $config['hooks'][$hook])) {
                        $config['hooks'][$hook][] = $name;
                    }
                }
            }
        }
        Cache::set('addons', $config);
    }
    Config::set('addons', $config);
});

// 闭包初始化行為
Hook::add('action_begin', function () {
    // 取得系统配置
    $data = App::$debug ? [] : Cache::get('hooks', []);
    $addons = (array)Config::get('addons.hooks');
    if (empty($data)) {
        // 初始化钩子
        foreach ($addons as $key => $values) {
            if (is_string($values)) {
                $values = explode(',', $values);
            } else {
                $values = (array)$values;
            }
            $addons[$key] = array_filter(array_map('get_addon_class', $values));
            Hook::add($key, $addons[$key]);
        }
        Cache::set('hooks', $addons);
    } else {
        Hook::import($data, false);
    }
});

/**
 * 處理插件钩子
 * @param string $hook 钩子名稱
 * @param mixed $params 傳入参數
 * @return void
 */
function hook($hook, $params = [])
{
    Hook::listen($hook, $params);
}

/**
 * 取得插件類的類名
 * @param $name 插件名
 * @param string $type 返回命名空間類型
 * @param string $class 當前類名
 * @return string
 */
function get_addon_class($name, $type = 'hook', $class = null)
{
    $name = Loader::parseName($name);
    // 處理多级控制器情况
    if (!is_null($class) && strpos($class, '.')) {
        $class = explode('.', $class);
        foreach ($class as $key => $cls) {
            $class[$key] = Loader::parseName($cls, 1);
        }
        $class = implode('\\', $class);
    } else {
        $class = Loader::parseName(is_null($class) ? $name : $class, 1);
    }
    switch ($type) {
        case 'controller':
            $namespace = "\\addons\\" . $name . "\\controller\\" . $class;
            break;
        default:
            $namespace = "\\addons\\" . $name . "\\" . $class;
    }

    return class_exists($namespace) ? $namespace : '';
}

/**
 * 取得插件類的配置文件數组
 * @param string $name 插件名
 * @return array
 */
function get_addon_config($name)
{
    $class = get_addon_class($name);
    if (class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    } else {
        return [];
    }
}

/**
 * 插件显示内容里產生訪問插件的url
 * @param $url
 * @param array $param
 * @return bool|string
 * @param bool|string $suffix 產生的URL後缀
 * @param bool|string $domain 域名
 */
function addon_url($url, $param = [], $suffix = true, $domain = false)
{
    $url = parse_url($url);
    $case = config('url_convert');
    $addons = $case ? Loader::parseName($url['scheme']) : $url['scheme'];
    $controller = $case ? Loader::parseName($url['host']) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参數 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    // 產生插件連結新規則
    $actions = "{$addons}-{$controller}-{$action}";

    return url("addons/execute/{$actions}", $param, $suffix, $domain);
}