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

namespace think;

use think\exception\ClassNotFoundException;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\RouteNotFoundException;

/**
 * App 應用管理
 * @author liu21st <liu21st@gmail.com>
 */
class App
{
    /**
     * @var bool 是否初始化过
     */
    protected static $init = false;

    /**
     * @var string 當前模組路徑
     */
    public static $modulePath;

    /**
     * @var bool 應用调试模式
     */
    public static $debug = true;

    /**
     * @var string 應用類庫命名空間
     */
    public static $namespace = 'app';

    /**
     * @var bool 應用類庫後缀
     */
    public static $suffix = false;

    /**
     * @var bool 應用路由检测
     */
    protected static $routeCheck;

    /**
     * @var bool 严格路由检测
     */
    protected static $routeMust;

    /**
     * @var array 請求调度分发
     */
    protected static $dispatch;

    /**
     * @var array 額外載入文件
     */
    protected static $file = [];

    /**
     * 執行應用程序
     * @access public
     * @param  Request $request 請求對象
     * @return Response
     * @throws Exception
     */
    public static function run(Request $request = null)
    {
        $request = is_null($request) ? Request::instance() : $request;

        try {
            $config = self::initCommon();

            // 模組/控制器绑定
            if (defined('BIND_MODULE')) {
                BIND_MODULE && Route::bind(BIND_MODULE);
            } elseif ($config['auto_bind_module']) {
                // 入口自動绑定
                $name = pathinfo($request->baseFile(), PATHINFO_FILENAME);
                if ($name && 'index' != $name && is_dir(APP_PATH . $name)) {
                    Route::bind($name);
                }
            }

            $request->filter($config['default_filter']);

            // 默認語言
            Lang::range($config['default_lang']);
            // 開啟多語言機制 检测當前語言
            $config['lang_switch_on'] && Lang::detect();
            $request->langset(Lang::range());

            // 載入系统語言包
            Lang::load([
                THINK_PATH . 'lang' . DS . $request->langset() . EXT,
                APP_PATH . 'lang' . DS . $request->langset() . EXT,
            ]);

            // 监听 app_dispatch
            Hook::listen('app_dispatch', self::$dispatch);
            // 取得應用调度訊息
            $dispatch = self::$dispatch;

            // 未設定调度訊息則进行 URL 路由检测
            if (empty($dispatch)) {
                $dispatch = self::routeCheck($request, $config);
            }

            // 记录當前调度訊息
            $request->dispatch($dispatch);

            // 记录路由和請求訊息
            if (self::$debug) {
                Log::record('[ ROUTE ] ' . var_export($dispatch, true), 'info');
                Log::record('[ HEADER ] ' . var_export($request->header(), true), 'info');
                Log::record('[ PARAM ] ' . var_export($request->param(), true), 'info');
            }

            // 监听 app_begin
            Hook::listen('app_begin', $dispatch);

            // 請求快取檢查
            $request->cache(
                $config['request_cache'],
                $config['request_cache_expire'],
                $config['request_cache_except']
            );

            $data = self::exec($dispatch, $config);
        } catch (HttpResponseException $exception) {
            $data = $exception->getResponse();
        }

        // 清空類的實例化
        Loader::clearInstance();

        // 输出資料到客户端
        if ($data instanceof Response) {
            $response = $data;
        } elseif (!is_null($data)) {
            // 默認自動识别响应输出類型
            $type = $request->isAjax() ?
            Config::get('default_ajax_return') :
            Config::get('default_return_type');

            $response = Response::create($data, $type);
        } else {
            $response = Response::create();
        }

        // 监听 app_end
        Hook::listen('app_end', $response);

        return $response;
    }

    /**
     * 初始化應用，並返回配置訊息
     * @access public
     * @return array
     */
    public static function initCommon()
    {
        if (empty(self::$init)) {
            if (defined('APP_NAMESPACE')) {
                self::$namespace = APP_NAMESPACE;
            }

            Loader::addNamespace(self::$namespace, APP_PATH);

            // 初始化應用
            $config       = self::init();
            self::$suffix = $config['class_suffix'];

            // 應用调试模式
            self::$debug = Env::get('app_debug', Config::get('app_debug'));

            if (!self::$debug) {
                ini_set('display_errors', 'Off');
            } elseif (!IS_CLI) {
                // 重新申請一块比较大的 buffer
                if (ob_get_level() > 0) {
                    $output = ob_get_clean();
                }

                ob_start();

                if (!empty($output)) {
                    echo $output;
                }

            }

            if (!empty($config['root_namespace'])) {
                Loader::addNamespace($config['root_namespace']);
            }

            // 載入額外文件
            if (!empty($config['extra_file_list'])) {
                foreach ($config['extra_file_list'] as $file) {
                    $file = strpos($file, '.') ? $file : APP_PATH . $file . EXT;
                    if (is_file($file) && !isset(self::$file[$file])) {
                        include $file;
                        self::$file[$file] = true;
                    }
                }
            }

            // 設定系统时区
            date_default_timezone_set($config['default_timezone']);

            // 监听 app_init
            Hook::listen('app_init');

            self::$init = true;
        }

        return Config::get();
    }

    /**
     * 初始化應用或模組
     * @access public
     * @param string $module 模組名
     * @return array
     */
    private static function init($module = '')
    {
        // 定位模組目錄
        $module = $module ? $module . DS : '';

        // 載入初始化文件
        if (is_file(APP_PATH . $module . 'init' . EXT)) {
            include APP_PATH . $module . 'init' . EXT;
        } elseif (is_file(RUNTIME_PATH . $module . 'init' . EXT)) {
            include RUNTIME_PATH . $module . 'init' . EXT;
        } else {
            // 載入模組配置
            $config = Config::load(CONF_PATH . $module . 'config' . CONF_EXT);

            // 读取資料庫配置文件
            $filename = CONF_PATH . $module . 'database' . CONF_EXT;
            Config::load($filename, 'database');

            // 读取扩展配置文件
            if (is_dir(CONF_PATH . $module . 'extra')) {
                $dir   = CONF_PATH . $module . 'extra';
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ('.' . pathinfo($file, PATHINFO_EXTENSION) === CONF_EXT) {
                        $filename = $dir . DS . $file;
                        Config::load($filename, pathinfo($file, PATHINFO_FILENAME));
                    }
                }
            }

            // 載入應用狀態配置
            if ($config['app_status']) {
                Config::load(CONF_PATH . $module . $config['app_status'] . CONF_EXT);
            }

            // 載入行為扩展文件
            if (is_file(CONF_PATH . $module . 'tags' . EXT)) {
                Hook::import(include CONF_PATH . $module . 'tags' . EXT);
            }

            // 載入公共文件
            $path = APP_PATH . $module;
            if (is_file($path . 'common' . EXT)) {
                include $path . 'common' . EXT;
            }

            // 載入當前模組語言包
            if ($module) {
                Lang::load($path . 'lang' . DS . Request::instance()->langset() . EXT);
            }
        }

        return Config::get();
    }

    /**
     * 設定當前請求的调度訊息
     * @access public
     * @param array|string  $dispatch 调度訊息
     * @param string        $type     调度類型
     * @return void
     */
    public static function dispatch($dispatch, $type = 'module')
    {
        self::$dispatch = ['type' => $type, $type => $dispatch];
    }

    /**
     * 執行函數或者闭包方法 支援参數调用
     * @access public
     * @param string|array|\Closure $function 函數或者闭包
     * @param array                 $vars     变量
     * @return mixed
     */
    public static function invokeFunction($function, $vars = [])
    {
        $reflect = new \ReflectionFunction($function);
        $args    = self::bindParams($reflect, $vars);

        // 记录執行訊息
        self::$debug && Log::record('[ RUN ] ' . $reflect->__toString(), 'info');

        return $reflect->invokeArgs($args);
    }

    /**
     * 调用反射執行類的方法 支援参數绑定
     * @access public
     * @param string|array $method 方法
     * @param array        $vars   变量
     * @return mixed
     */
    public static function invokeMethod($method, $vars = [])
    {
        if (is_array($method)) {
            $class   = is_object($method[0]) ? $method[0] : self::invokeClass($method[0]);
            $reflect = new \ReflectionMethod($class, $method[1]);
        } else {
            // 静态方法
            $reflect = new \ReflectionMethod($method);
        }

        $args = self::bindParams($reflect, $vars);

        self::$debug && Log::record('[ RUN ] ' . $reflect->class . '->' . $reflect->name . '[ ' . $reflect->getFileName() . ' ]', 'info');

        return $reflect->invokeArgs(isset($class) ? $class : null, $args);
    }

    /**
     * 调用反射執行類的實例化 支援依赖注入
     * @access public
     * @param string $class 類名
     * @param array  $vars  变量
     * @return mixed
     */
    public static function invokeClass($class, $vars = [])
    {
        $reflect     = new \ReflectionClass($class);
        $constructor = $reflect->getConstructor();
        $args        = $constructor ? self::bindParams($constructor, $vars) : [];

        return $reflect->newInstanceArgs($args);
    }

    /**
     * 绑定参數
     * @access private
     * @param \ReflectionMethod|\ReflectionFunction $reflect 反射類
     * @param array                                 $vars    变量
     * @return array
     */
    private static function bindParams($reflect, $vars = [])
    {
        // 自動取得請求变量
        if (empty($vars)) {
            $vars = Config::get('url_param_type') ?
            Request::instance()->route() :
            Request::instance()->param();
        }

        $args = [];
        if ($reflect->getNumberOfParameters() > 0) {
            // 判断數组類型 數字數组时按顺序绑定参數
            reset($vars);
            $type = key($vars) === 0 ? 1 : 0;

            foreach ($reflect->getParameters() as $param) {
                $args[] = self::getParamValue($param, $vars, $type);
            }
        }

        return $args;
    }

    /**
     * 取得参數值
     * @access private
     * @param \ReflectionParameter  $param 参數
     * @param array                 $vars  变量
     * @param string                $type  類别
     * @return array
     */
    private static function getParamValue($param, &$vars, $type)
    {
        $name  = $param->getName();
        $class = $param->getClass();

        if ($class) {
            $className = $class->getName();
            $bind      = Request::instance()->$name;

            if ($bind instanceof $className) {
                $result = $bind;
            } else {
                if (method_exists($className, 'invoke')) {
                    $method = new \ReflectionMethod($className, 'invoke');

                    if ($method->isPublic() && $method->isStatic()) {
                        return $className::invoke(Request::instance());
                    }
                }

                $result = method_exists($className, 'instance') ?
                $className::instance() :
                new $className;
            }
        } elseif (1 == $type && !empty($vars)) {
            $result = array_shift($vars);
        } elseif (0 == $type && isset($vars[$name])) {
            $result = $vars[$name];
        } elseif ($param->isDefaultValueAvailable()) {
            $result = $param->getDefaultValue();
        } else {
            throw new \InvalidArgumentException('method param miss:' . $name);
        }

        return $result;
    }

    /**
     * 執行调用分发
     * @access protected
     * @param array $dispatch 调用訊息
     * @param array $config   配置訊息
     * @return Response|mixed
     * @throws \InvalidArgumentException
     */
    protected static function exec($dispatch, $config)
    {
        switch ($dispatch['type']) {
            case 'redirect': // 重定向跳转
                $data = Response::create($dispatch['url'], 'redirect')
                    ->code($dispatch['status']);
                break;
            case 'module': // 模組/控制器/操作
                $data = self::module(
                    $dispatch['module'],
                    $config,
                    isset($dispatch['convert']) ? $dispatch['convert'] : null
                );
                break;
            case 'controller': // 執行控制器操作
                $vars = array_merge(Request::instance()->param(), $dispatch['var']);
                $data = Loader::action(
                    $dispatch['controller'],
                    $vars,
                    $config['url_controller_layer'],
                    $config['controller_suffix']
                );
                break;
            case 'method': // 回调方法
                $vars = array_merge(Request::instance()->param(), $dispatch['var']);
                $data = self::invokeMethod($dispatch['method'], $vars);
                break;
            case 'function': // 闭包
                $data = self::invokeFunction($dispatch['function']);
                break;
            case 'response': // Response 實例
                $data = $dispatch['response'];
                break;
            default:
                throw new \InvalidArgumentException('dispatch type not support');
        }

        return $data;
    }

    /**
     * 執行模組
     * @access public
     * @param array $result  模組/控制器/操作
     * @param array $config  配置参數
     * @param bool  $convert 是否自動轉換控制器和操作名
     * @return mixed
     * @throws HttpException
     */
    public static function module($result, $config, $convert = null)
    {
        if (is_string($result)) {
            $result = explode('/', $result);
        }

        $request = Request::instance();

        if ($config['app_multi_module']) {
            // 多模組部署
            $module    = strip_tags(strtolower($result[0] ?: $config['default_module']));
            $bind      = Route::getBind('module');
            $available = false;

            if ($bind) {
                // 绑定模組
                list($bindModule) = explode('/', $bind);

                if (empty($result[0])) {
                    $module    = $bindModule;
                    $available = true;
                } elseif ($module == $bindModule) {
                    $available = true;
                }
            } elseif (!in_array($module, $config['deny_module_list']) && is_dir(APP_PATH . $module)) {
                $available = true;
            }

            // 模組初始化
            if ($module && $available) {
                // 初始化模組
                $request->module($module);
                $config = self::init($module);

                // 模組請求快取檢查
                $request->cache(
                    $config['request_cache'],
                    $config['request_cache_expire'],
                    $config['request_cache_except']
                );
            } else {
                throw new HttpException(404, 'module not exists:' . $module);
            }
        } else {
            // 單一模組部署
            $module = '';
            $request->module($module);
        }

        // 設定默認过滤機制
        $request->filter($config['default_filter']);

        // 當前模組路徑
        App::$modulePath = APP_PATH . ($module ? $module . DS : '');

        // 是否自動轉換控制器和操作名
        $convert = is_bool($convert) ? $convert : $config['url_convert'];

        // 取得控制器名
        $controller = strip_tags($result[1] ?: $config['default_controller']);

        if (!preg_match('/^[A-Za-z](\w|\.)*$/', $controller)) {
            throw new HttpException(404, 'controller not exists:' . $controller);
        }

        $controller = $convert ? strtolower($controller) : $controller;

        // 取得操作名
        $actionName = strip_tags($result[2] ?: $config['default_action']);
        if (!empty($config['action_convert'])) {
            $actionName = Loader::parseName($actionName, 1);
        } else {
            $actionName = $convert ? strtolower($actionName) : $actionName;
        }

        // 設定當前請求的控制器、操作
        $request->controller(Loader::parseName($controller, 1))->action($actionName);

        // 监听module_init
        Hook::listen('module_init', $request);

        try {
            $instance = Loader::controller(
                $controller,
                $config['url_controller_layer'],
                $config['controller_suffix'],
                $config['empty_controller']
            );
        } catch (ClassNotFoundException $e) {
            throw new HttpException(404, 'controller not exists:' . $e->getClass());
        }

        // 取得當前操作名
        $action = $actionName . $config['action_suffix'];

        $vars = [];
        if (is_callable([$instance, $action])) {
            // 執行操作方法
            $call = [$instance, $action];
            // 严格取得當前操作方法名
            $reflect    = new \ReflectionMethod($instance, $action);
            $methodName = $reflect->getName();
            $suffix     = $config['action_suffix'];
            $actionName = $suffix ? substr($methodName, 0, -strlen($suffix)) : $methodName;
            $request->action($actionName);

        } elseif (is_callable([$instance, '_empty'])) {
            // 空操作
            $call = [$instance, '_empty'];
            $vars = [$actionName];
        } else {
            // 操作不存在
            throw new HttpException(404, 'method not exists:' . get_class($instance) . '->' . $action . '()');
        }

        Hook::listen('action_begin', $call);

        return self::invokeMethod($call, $vars);
    }

    /**
     * URL路由检测（根據PATH_INFO)
     * @access public
     * @param  \think\Request $request 請求實例
     * @param  array          $config  配置訊息
     * @return array
     * @throws \think\Exception
     */
    public static function routeCheck($request, array $config)
    {
        $path   = $request->path();
        $depr   = $config['pathinfo_depr'];
        $result = false;

        // 路由检测
        $check = !is_null(self::$routeCheck) ? self::$routeCheck : $config['url_route_on'];
        if ($check) {
            // 開啟路由
            if (is_file(RUNTIME_PATH . 'route.php')) {
                // 读取路由快取
                $rules = include RUNTIME_PATH . 'route.php';
                is_array($rules) && Route::rules($rules);
            } else {
                $files = $config['route_config_file'];
                foreach ($files as $file) {
                    if (is_file(CONF_PATH . $file . CONF_EXT)) {
                        // 匯入路由配置
                        $rules = include CONF_PATH . $file . CONF_EXT;
                        is_array($rules) && Route::import($rules);
                    }
                }
            }

            // 路由检测（根據路由定義返回不同的URL调度）
            $result = Route::check($request, $path, $depr, $config['url_domain_deploy']);
            $must   = !is_null(self::$routeMust) ? self::$routeMust : $config['url_route_must'];

            if ($must && false === $result) {
                // 路由無效
                throw new RouteNotFoundException();
            }
        }

        // 路由無效 解析模組/控制器/操作/参數... 支援控制器自動搜索
        if (false === $result) {
            $result = Route::parseUrl($path, $depr, $config['controller_auto_search']);
        }

        return $result;
    }

    /**
     * 設定應用的路由检测機制
     * @access public
     * @param  bool $route 是否需要检测路由
     * @param  bool $must  是否强制检测路由
     * @return void
     */
    public static function route($route, $must = false)
    {
        self::$routeCheck = $route;
        self::$routeMust  = $must;
    }
}
