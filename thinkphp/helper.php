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

//------------------------
// ThinkPHP 助手函數
//-------------------------

use think\Cache;
use think\Config;
use think\Cookie;
use think\Db;
use think\Debug;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\Lang;
use think\Loader;
use think\Log;
use think\Model;
use think\Request;
use think\Response;
use think\Session;
use think\Url;
use think\View;

if (!function_exists('load_trait')) {
    /**
     * 快速匯入Traits PHP5.5以上無需调用
     * @param string    $class trait庫
     * @param string    $ext 類庫後缀
     * @return boolean
     */
    function load_trait($class, $ext = EXT)
    {
        return Loader::import($class, TRAIT_PATH, $ext);
    }
}

if (!function_exists('exception')) {
    /**
     * 抛出异常處理
     *
     * @param string    $msg  异常消息
     * @param integer   $code 异常程式碼 默認為0
     * @param string    $exception 异常類
     *
     * @throws Exception
     */
    function exception($msg, $code = 0, $exception = '')
    {
        $e = $exception ?: '\think\Exception';
        throw new $e($msg, $code);
    }
}

if (!function_exists('debug')) {
    /**
     * 记录時間（微秒）和内存使用情况
     * @param string            $start 開始标签
     * @param string            $end 结束标签
     * @param integer|string    $dec 小數位 如果是m 表示统计内存占用
     * @return mixed
     */
    function debug($start, $end = '', $dec = 6)
    {
        if ('' == $end) {
            Debug::remark($start);
        } else {
            return 'm' == $dec ? Debug::getRangeMem($start, $end) : Debug::getRangeTime($start, $end, $dec);
        }
    }
}

if (!function_exists('lang')) {
    /**
     * 取得語言变量值
     * @param string    $name 語言变量名
     * @param array     $vars 動态变量值
     * @param string    $lang 語言
     * @return mixed
     */
    function lang($name, $vars = [], $lang = '')
    {
        return Lang::get($name, $vars, $lang);
    }
}

if (!function_exists('config')) {
    /**
     * 取得和設定配置参數
     * @param string|array  $name 参數名
     * @param mixed         $value 参數值
     * @param string        $range 作用域
     * @return mixed
     */
    function config($name = '', $value = null, $range = '')
    {
        if (is_null($value) && is_string($name)) {
            return 0 === strpos($name, '?') ? Config::has(substr($name, 1), $range) : Config::get($name, $range);
        } else {
            return Config::set($name, $value, $range);
        }
    }
}

if (!function_exists('input')) {
    /**
     * 取得输入資料 支援默認值和过滤
     * @param string    $key 取得的变量名
     * @param mixed     $default 默認值
     * @param string    $filter 过滤方法
     * @return mixed
     */
    function input($key = '', $default = null, $filter = '')
    {
        if (0 === strpos($key, '?')) {
            $key = substr($key, 1);
            $has = true;
        }
        if ($pos = strpos($key, '.')) {
            // 指定参數来源
            list($method, $key) = explode('.', $key, 2);
            if (!in_array($method, ['get', 'post', 'put', 'patch', 'delete', 'route', 'param', 'request', 'session', 'cookie', 'server', 'env', 'path', 'file'])) {
                $key    = $method . '.' . $key;
                $method = 'param';
            }
        } else {
            // 默認為自動判断
            $method = 'param';
        }
        if (isset($has)) {
            return request()->has($key, $method, $default);
        } else {
            return request()->$method($key, $default, $filter);
        }
    }
}

if (!function_exists('widget')) {
    /**
     * 渲染输出Widget
     * @param string    $name Widget名稱
     * @param array     $data 傳入的参數
     * @return mixed
     */
    function widget($name, $data = [])
    {
        return Loader::action($name, $data, 'widget');
    }
}

if (!function_exists('model')) {
    /**
     * 實例化Model
     * @param string    $name Model名稱
     * @param string    $layer 業務層名稱
     * @param bool      $appendSuffix 是否新增類名後缀
     * @return \think\Model
     */
    function model($name = '', $layer = 'model', $appendSuffix = false)
    {
        return Loader::model($name, $layer, $appendSuffix);
    }
}

if (!function_exists('validate')) {
    /**
     * 實例化驗證器
     * @param string    $name 驗證器名稱
     * @param string    $layer 業務層名稱
     * @param bool      $appendSuffix 是否新增類名後缀
     * @return \think\Validate
     */
    function validate($name = '', $layer = 'validate', $appendSuffix = false)
    {
        return Loader::validate($name, $layer, $appendSuffix);
    }
}

if (!function_exists('db')) {
    /**
     * 實例化資料庫類
     * @param string        $name 操作的資料表名稱（不含前缀）
     * @param array|string  $config 資料庫配置参數
     * @param bool          $force 是否强制重新連結
     * @return \think\db\Query
     */
    function db($name = '', $config = [], $force = false)
    {
        return Db::connect($config, $force)->name($name);
    }
}

if (!function_exists('controller')) {
    /**
     * 實例化控制器 格式：[模組/]控制器
     * @param string    $name 資源地址
     * @param string    $layer 控制層名稱
     * @param bool      $appendSuffix 是否新增類名後缀
     * @return \think\Controller
     */
    function controller($name, $layer = 'controller', $appendSuffix = false)
    {
        return Loader::controller($name, $layer, $appendSuffix);
    }
}

if (!function_exists('action')) {
    /**
     * 调用模組的操作方法 参數格式 [模組/控制器/]操作
     * @param string        $url 调用地址
     * @param string|array  $vars 调用参數 支援字符串和數组
     * @param string        $layer 要调用的控制層名稱
     * @param bool          $appendSuffix 是否新增類名後缀
     * @return mixed
     */
    function action($url, $vars = [], $layer = 'controller', $appendSuffix = false)
    {
        return Loader::action($url, $vars, $layer, $appendSuffix);
    }
}

if (!function_exists('import')) {
    /**
     * 匯入所需的類庫 同java的Import 本函數有快取功能
     * @param string    $class 類庫命名空間字符串
     * @param string    $baseUrl 起始路徑
     * @param string    $ext 匯入的文件扩展名
     * @return boolean
     */
    function import($class, $baseUrl = '', $ext = EXT)
    {
        return Loader::import($class, $baseUrl, $ext);
    }
}

if (!function_exists('vendor')) {
    /**
     * 快速匯入第三方框架類庫 所有第三方框架的類庫文件统一放到 系统的Vendor目錄下面
     * @param string    $class 類庫
     * @param string    $ext 類庫後缀
     * @return boolean
     */
    function vendor($class, $ext = EXT)
    {
        return Loader::import($class, VENDOR_PATH, $ext);
    }
}

if (!function_exists('dump')) {
    /**
     * 浏览器友好的变量输出
     * @param mixed     $var 变量
     * @param boolean   $echo 是否输出 默認為true 如果為false 則返回输出字符串
     * @param string    $label 标签 默認為空
     * @return void|string
     */
    function dump($var, $echo = true, $label = null)
    {
        return Debug::dump($var, $echo, $label);
    }
}

if (!function_exists('url')) {
    /**
     * Url產生
     * @param string        $url 路由地址
     * @param string|array  $vars 变量
     * @param bool|string   $suffix 產生的URL後缀
     * @param bool|string   $domain 域名
     * @return string
     */
    function url($url = '', $vars = '', $suffix = true, $domain = false)
    {
        return Url::build($url, $vars, $suffix, $domain);
    }
}

if (!function_exists('session')) {
    /**
     * Session管理
     * @param string|array  $name session名稱，如果為數组表示进行session設定
     * @param mixed         $value session值
     * @param string        $prefix 前缀
     * @return mixed
     */
    function session($name, $value = '', $prefix = null)
    {
        if (is_array($name)) {
            // 初始化
            Session::init($name);
        } elseif (is_null($name)) {
            // 清除
            Session::clear('' === $value ? null : $value);
        } elseif ('' === $value) {
            // 判断或取得
            return 0 === strpos($name, '?') ? Session::has(substr($name, 1), $prefix) : Session::get($name, $prefix);
        } elseif (is_null($value)) {
            // 刪除
            return Session::delete($name, $prefix);
        } else {
            // 設定
            return Session::set($name, $value, $prefix);
        }
    }
}

if (!function_exists('cookie')) {
    /**
     * Cookie管理
     * @param string|array  $name cookie名稱，如果為數组表示进行cookie設定
     * @param mixed         $value cookie值
     * @param mixed         $option 参數
     * @return mixed
     */
    function cookie($name, $value = '', $option = null)
    {
        if (is_array($name)) {
            // 初始化
            Cookie::init($name);
        } elseif (is_null($name)) {
            // 清除
            Cookie::clear($value);
        } elseif ('' === $value) {
            // 取得
            return 0 === strpos($name, '?') ? Cookie::has(substr($name, 1), $option) : Cookie::get($name, $option);
        } elseif (is_null($value)) {
            // 刪除
            return Cookie::delete($name);
        } else {
            // 設定
            return Cookie::set($name, $value, $option);
        }
    }
}

if (!function_exists('cache')) {
    /**
     * 快取管理
     * @param mixed     $name 快取名稱，如果為數组表示进行快取設定
     * @param mixed     $value 快取值
     * @param mixed     $options 快取参數
     * @param string    $tag 快取标签
     * @return mixed
     */
    function cache($name, $value = '', $options = null, $tag = null)
    {
        if (is_array($options)) {
            // 快取操作的同时初始化
            $cache = Cache::connect($options);
        } elseif (is_array($name)) {
            // 快取初始化
            return Cache::connect($name);
        } else {
            $cache = Cache::init();
        }

        if (is_null($name)) {
            return $cache->clear($value);
        } elseif ('' === $value) {
            // 取得快取
            return 0 === strpos($name, '?') ? $cache->has(substr($name, 1)) : $cache->get($name);
        } elseif (is_null($value)) {
            // 刪除快取
            return $cache->rm($name);
        } elseif (0 === strpos($name, '?') && '' !== $value) {
            $expire = is_numeric($options) ? $options : null;
            return $cache->remember(substr($name, 1), $value, $expire);
        } else {
            // 快取資料
            if (is_array($options)) {
                $expire = isset($options['expire']) ? $options['expire'] : null; //修复查詢快取無法設定过期時間
            } else {
                $expire = is_numeric($options) ? $options : null; //默認快速快取設定过期時間
            }
            if (is_null($tag)) {
                return $cache->set($name, $value, $expire);
            } else {
                return $cache->tag($tag)->set($name, $value, $expire);
            }
        }
    }
}

if (!function_exists('trace')) {
    /**
     * 记录日誌訊息
     * @param mixed     $log log訊息 支援字符串和數组
     * @param string    $level 日誌级别
     * @return void|array
     */
    function trace($log = '[think]', $level = 'log')
    {
        if ('[think]' === $log) {
            return Log::getLog();
        } else {
            Log::record($log, $level);
        }
    }
}

if (!function_exists('request')) {
    /**
     * 取得當前Request對象實例
     * @return Request
     */
    function request()
    {
        return Request::instance();
    }
}

if (!function_exists('response')) {
    /**
     * 建立普通 Response 對象實例
     * @param mixed      $data   输出資料
     * @param int|string $code   狀態碼
     * @param array      $header 头訊息
     * @param string     $type
     * @return Response
     */
    function response($data = [], $code = 200, $header = [], $type = 'html')
    {
        return Response::create($data, $type, $code, $header);
    }
}

if (!function_exists('view')) {
    /**
     * 渲染模板输出
     * @param string    $template 模板文件
     * @param array     $vars 模板变量
     * @param array     $replace 模板替换
     * @param integer   $code 狀態碼
     * @return \think\response\View
     */
    function view($template = '', $vars = [], $replace = [], $code = 200)
    {
        return Response::create($template, 'view', $code)->replace($replace)->assign($vars);
    }
}

if (!function_exists('json')) {
    /**
     * 取得\think\response\Json對象實例
     * @param mixed   $data 返回的資料
     * @param integer $code 狀態碼
     * @param array   $header 头部
     * @param array   $options 参數
     * @return \think\response\Json
     */
    function json($data = [], $code = 200, $header = [], $options = [])
    {
        return Response::create($data, 'json', $code, $header, $options);
    }
}

if (!function_exists('jsonp')) {
    /**
     * 取得\think\response\Jsonp對象實例
     * @param mixed   $data    返回的資料
     * @param integer $code    狀態碼
     * @param array   $header 头部
     * @param array   $options 参數
     * @return \think\response\Jsonp
     */
    function jsonp($data = [], $code = 200, $header = [], $options = [])
    {
        return Response::create($data, 'jsonp', $code, $header, $options);
    }
}

if (!function_exists('xml')) {
    /**
     * 取得\think\response\Xml對象實例
     * @param mixed   $data    返回的資料
     * @param integer $code    狀態碼
     * @param array   $header  头部
     * @param array   $options 参數
     * @return \think\response\Xml
     */
    function xml($data = [], $code = 200, $header = [], $options = [])
    {
        return Response::create($data, 'xml', $code, $header, $options);
    }
}

if (!function_exists('redirect')) {
    /**
     * 取得\think\response\Redirect對象實例
     * @param mixed         $url 重定向地址 支援Url::build方法的地址
     * @param array|integer $params 額外参數
     * @param integer       $code 狀態碼
     * @param array         $with 隐式傳参
     * @return \think\response\Redirect
     */
    function redirect($url = [], $params = [], $code = 302, $with = [])
    {
        if (is_integer($params)) {
            $code   = $params;
            $params = [];
        }
        return Response::create($url, 'redirect', $code)->params($params)->with($with);
    }
}

if (!function_exists('abort')) {
    /**
     * 抛出HTTP异常
     * @param integer|Response      $code 狀態碼 或者 Response對象實例
     * @param string                $message 錯誤訊息
     * @param array                 $header 参數
     */
    function abort($code, $message = null, $header = [])
    {
        if ($code instanceof Response) {
            throw new HttpResponseException($code);
        } else {
            throw new HttpException($code, $message, null, $header);
        }
    }
}

if (!function_exists('halt')) {
    /**
     * 调试变量並且中斷输出
     * @param mixed      $var 调试变量或者訊息
     */
    function halt($var)
    {
        dump($var);
        throw new HttpResponseException(new Response);
    }
}

if (!function_exists('token')) {
    /**
     * 產生表單令牌
     * @param string $name 令牌名稱
     * @param mixed  $type 令牌產生方法
     * @return string
     */
    function token($name = '__token__', $type = 'md5')
    {
        $token = Request::instance()->token($name, $type);
        return '<input type="hidden" name="' . $name . '" value="' . $token . '" />';
    }
}

if (!function_exists('load_relation')) {
    /**
     * 延迟预载入关联查詢
     * @param mixed $resultSet 資料集
     * @param mixed $relation 关联
     * @return array
     */
    function load_relation($resultSet, $relation)
    {
        $item = current($resultSet);
        if ($item instanceof Model) {
            $item->eagerlyResultSet($resultSet, $relation);
        }
        return $resultSet;
    }
}

if (!function_exists('collection')) {
    /**
     * 數组轉換為資料集對象
     * @param array $resultSet 資料集數组
     * @return \think\model\Collection|\think\Collection
     */
    function collection($resultSet)
    {
        $item = current($resultSet);
        if ($item instanceof Model) {
            return \think\model\Collection::make($resultSet);
        } else {
            return \think\Collection::make($resultSet);
        }
    }
}
