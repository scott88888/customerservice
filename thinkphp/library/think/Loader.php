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

class Loader
{
    /**
     * @var array 實例數组
     */
    protected static $instance = [];

    /**
     * @var array 類名映射
     */
    protected static $classMap = [];

    /**
     * @var array 命名空間别名
     */
    protected static $namespaceAlias = [];

    /**
     * @var array PSR-4 命名空間前缀長度映射
     */
    private static $prefixLengthsPsr4 = [];

    /**
     * @var array PSR-4 的載入目錄
     */
    private static $prefixDirsPsr4 = [];

    /**
     * @var array PSR-4 載入失敗的回退目錄
     */
    private static $fallbackDirsPsr4 = [];

    /**
     * @var array PSR-0 命名空間前缀映射
     */
    private static $prefixesPsr0 = [];

    /**
     * @var array PSR-0 載入失敗的回退目錄
     */
    private static $fallbackDirsPsr0 = [];

    /**
     * @var array 需要載入的文件
     */
    private static $files = [];

    /**
     * 自動載入
     * @access public
     * @param  string $class 類名
     * @return bool
     */
    public static function autoload($class)
    {
        // 检测命名空間别名
        if (!empty(self::$namespaceAlias)) {
            $namespace = dirname($class);
            if (isset(self::$namespaceAlias[$namespace])) {
                $original = self::$namespaceAlias[$namespace] . '\\' . basename($class);
                if (class_exists($original)) {
                    return class_alias($original, $class, false);
                }
            }
        }

        if ($file = self::findFile($class)) {
            // 非 Win 环境不严格区分大小寫
            if (!IS_WIN || pathinfo($file, PATHINFO_FILENAME) == pathinfo(realpath($file), PATHINFO_FILENAME)) {
                __include_file($file);
                return true;
            }
        }

        return false;
    }

    /**
     * 查找文件
     * @access private
     * @param  string $class 類名
     * @return bool|string
     */
    private static function findFile($class)
    {
        // 類庫映射
        if (!empty(self::$classMap[$class])) {
            return self::$classMap[$class];
        }

        // 查找 PSR-4
        $logicalPathPsr4 = strtr($class, '\\', DS) . EXT;
        $first           = $class[0];

        if (isset(self::$prefixLengthsPsr4[$first])) {
            foreach (self::$prefixLengthsPsr4[$first] as $prefix => $length) {
                if (0 === strpos($class, $prefix)) {
                    foreach (self::$prefixDirsPsr4[$prefix] as $dir) {
                        if (is_file($file = $dir . DS . substr($logicalPathPsr4, $length))) {
                            return $file;
                        }
                    }
                }
            }
        }

        // 查找 PSR-4 fallback dirs
        foreach (self::$fallbackDirsPsr4 as $dir) {
            if (is_file($file = $dir . DS . $logicalPathPsr4)) {
                return $file;
            }
        }

        // 查找 PSR-0
        if (false !== $pos = strrpos($class, '\\')) {
            // namespace class name
            $logicalPathPsr0 = substr($logicalPathPsr4, 0, $pos + 1)
            . strtr(substr($logicalPathPsr4, $pos + 1), '_', DS);
        } else {
            // PEAR-like class name
            $logicalPathPsr0 = strtr($class, '_', DS) . EXT;
        }

        if (isset(self::$prefixesPsr0[$first])) {
            foreach (self::$prefixesPsr0[$first] as $prefix => $dirs) {
                if (0 === strpos($class, $prefix)) {
                    foreach ($dirs as $dir) {
                        if (is_file($file = $dir . DS . $logicalPathPsr0)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // 查找 PSR-0 fallback dirs
        foreach (self::$fallbackDirsPsr0 as $dir) {
            if (is_file($file = $dir . DS . $logicalPathPsr0)) {
                return $file;
            }
        }

        // 找不到則設定映射為 false 並返回
        return self::$classMap[$class] = false;
    }

    /**
     * 註冊 classmap
     * @access public
     * @param  string|array $class 類名
     * @param  string       $map   映射
     * @return void
     */
    public static function addClassMap($class, $map = '')
    {
        if (is_array($class)) {
            self::$classMap = array_merge(self::$classMap, $class);
        } else {
            self::$classMap[$class] = $map;
        }
    }

    /**
     * 註冊命名空間
     * @access public
     * @param  string|array $namespace 命名空間
     * @param  string       $path      路徑
     * @return void
     */
    public static function addNamespace($namespace, $path = '')
    {
        if (is_array($namespace)) {
            foreach ($namespace as $prefix => $paths) {
                self::addPsr4($prefix . '\\', rtrim($paths, DS), true);
            }
        } else {
            self::addPsr4($namespace . '\\', rtrim($path, DS), true);
        }
    }

    /**
     * 新增 PSR-0 命名空間
     * @access private
     * @param  array|string $prefix  空間前缀
     * @param  array        $paths   路徑
     * @param  bool         $prepend 预先設定的優先级更高
     * @return void
     */
    private static function addPsr0($prefix, $paths, $prepend = false)
    {
        if (!$prefix) {
            self::$fallbackDirsPsr0 = $prepend ?
            array_merge((array) $paths, self::$fallbackDirsPsr0) :
            array_merge(self::$fallbackDirsPsr0, (array) $paths);
        } else {
            $first = $prefix[0];

            if (!isset(self::$prefixesPsr0[$first][$prefix])) {
                self::$prefixesPsr0[$first][$prefix] = (array) $paths;
            } else {
                self::$prefixesPsr0[$first][$prefix] = $prepend ?
                array_merge((array) $paths, self::$prefixesPsr0[$first][$prefix]) :
                array_merge(self::$prefixesPsr0[$first][$prefix], (array) $paths);
            }
        }
    }

    /**
     * 新增 PSR-4 空間
     * @access private
     * @param  array|string $prefix  空間前缀
     * @param  string       $paths   路徑
     * @param  bool         $prepend 预先設定的優先级更高
     * @return void
     */
    private static function addPsr4($prefix, $paths, $prepend = false)
    {
        if (!$prefix) {
            // Register directories for the root namespace.
            self::$fallbackDirsPsr4 = $prepend ?
            array_merge((array) $paths, self::$fallbackDirsPsr4) :
            array_merge(self::$fallbackDirsPsr4, (array) $paths);

        } elseif (!isset(self::$prefixDirsPsr4[$prefix])) {
            // Register directories for a new namespace.
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new \InvalidArgumentException(
                    "A non-empty PSR-4 prefix must end with a namespace separator."
                );
            }

            self::$prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            self::$prefixDirsPsr4[$prefix]                = (array) $paths;

        } else {
            self::$prefixDirsPsr4[$prefix] = $prepend ?
            // Prepend directories for an already registered namespace.
            array_merge((array) $paths, self::$prefixDirsPsr4[$prefix]) :
            // Append directories for an already registered namespace.
            array_merge(self::$prefixDirsPsr4[$prefix], (array) $paths);
        }
    }

    /**
     * 註冊命名空間别名
     * @access public
     * @param  array|string $namespace 命名空間
     * @param  string       $original  源文件
     * @return void
     */
    public static function addNamespaceAlias($namespace, $original = '')
    {
        if (is_array($namespace)) {
            self::$namespaceAlias = array_merge(self::$namespaceAlias, $namespace);
        } else {
            self::$namespaceAlias[$namespace] = $original;
        }
    }

    /**
     * 註冊自動載入機制
     * @access public
     * @param  callable $autoload 自動載入處理方法
     * @return void
     */
    public static function register($autoload = null)
    {
        // 註冊系统自動載入
        spl_autoload_register($autoload ?: 'think\\Loader::autoload', true, true);

        // Composer 自動載入支援
        if (is_dir(VENDOR_PATH . 'composer')) {
            if (PHP_VERSION_ID >= 50600 && is_file(VENDOR_PATH . 'composer' . DS . 'autoload_static.php')) {
                require VENDOR_PATH . 'composer' . DS . 'autoload_static.php';

                $declaredClass = get_declared_classes();
                $composerClass = array_pop($declaredClass);

                foreach (['prefixLengthsPsr4', 'prefixDirsPsr4', 'fallbackDirsPsr4', 'prefixesPsr0', 'fallbackDirsPsr0', 'classMap', 'files'] as $attr) {
                    if (property_exists($composerClass, $attr)) {
                        self::${$attr} = $composerClass::${$attr};
                    }
                }
            } else {
                self::registerComposerLoader();
            }
        }

        // 註冊命名空間定義
        self::addNamespace([
            'think'    => LIB_PATH . 'think' . DS,
            'behavior' => LIB_PATH . 'behavior' . DS,
            'traits'   => LIB_PATH . 'traits' . DS,
        ]);

        // 載入類庫映射文件
        if (is_file(RUNTIME_PATH . 'classmap' . EXT)) {
            self::addClassMap(__include_file(RUNTIME_PATH . 'classmap' . EXT));
        }

        self::loadComposerAutoloadFiles();

        // 自動載入 extend 目錄
        self::$fallbackDirsPsr4[] = rtrim(EXTEND_PATH, DS);
    }

    /**
     * 註冊 composer 自動載入
     * @access private
     * @return void
     */
    private static function registerComposerLoader()
    {
        if (is_file(VENDOR_PATH . 'composer/autoload_namespaces.php')) {
            $map = require VENDOR_PATH . 'composer/autoload_namespaces.php';
            foreach ($map as $namespace => $path) {
                self::addPsr0($namespace, $path);
            }
        }

        if (is_file(VENDOR_PATH . 'composer/autoload_psr4.php')) {
            $map = require VENDOR_PATH . 'composer/autoload_psr4.php';
            foreach ($map as $namespace => $path) {
                self::addPsr4($namespace, $path);
            }
        }

        if (is_file(VENDOR_PATH . 'composer/autoload_classmap.php')) {
            $classMap = require VENDOR_PATH . 'composer/autoload_classmap.php';
            if ($classMap) {
                self::addClassMap($classMap);
            }
        }

        if (is_file(VENDOR_PATH . 'composer/autoload_files.php')) {
            self::$files = require VENDOR_PATH . 'composer/autoload_files.php';
        }
    }

    // 載入composer autofile文件
    public static function loadComposerAutoloadFiles()
    {
        foreach (self::$files as $fileIdentifier => $file) {
            if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
                __require_file($file);

                $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;
            }
        }
    }

    /**
     * 匯入所需的類庫 同 Java 的 Import 本函數有快取功能
     * @access public
     * @param  string $class   類庫命名空間字符串
     * @param  string $baseUrl 起始路徑
     * @param  string $ext     匯入的文件扩展名
     * @return bool
     */
    public static function import($class, $baseUrl = '', $ext = EXT)
    {
        static $_file = [];
        $key          = $class . $baseUrl;
        $class        = str_replace(['.', '#'], [DS, '.'], $class);

        if (isset($_file[$key])) {
            return true;
        }

        if (empty($baseUrl)) {
            list($name, $class) = explode(DS, $class, 2);

            if (isset(self::$prefixDirsPsr4[$name . '\\'])) {
                // 註冊的命名空間
                $baseUrl = self::$prefixDirsPsr4[$name . '\\'];
            } elseif ('@' == $name) {
                // 載入當前模組應用類庫
                $baseUrl = App::$modulePath;
            } elseif (is_dir(EXTEND_PATH . $name)) {
                $baseUrl = EXTEND_PATH . $name . DS;
            } else {
                // 載入其它模組的類庫
                $baseUrl = APP_PATH . $name . DS;
            }
        } elseif (substr($baseUrl, -1) != DS) {
            $baseUrl .= DS;
        }

        // 如果類存在則匯入類庫文件
        if (is_array($baseUrl)) {
            foreach ($baseUrl as $path) {
                if (is_file($filename = $path . DS . $class . $ext)) {
                    break;
                }
            }
        } else {
            $filename = $baseUrl . $class . $ext;
        }

        if (!empty($filename) &&
            is_file($filename) &&
            (!IS_WIN || pathinfo($filename, PATHINFO_FILENAME) == pathinfo(realpath($filename), PATHINFO_FILENAME))
        ) {
            __include_file($filename);
            $_file[$key] = true;

            return true;
        }

        return false;
    }

    /**
     * 實例化（分層）模型
     * @access public
     * @param  string $name         Model名稱
     * @param  string $layer        業務層名稱
     * @param  bool   $appendSuffix 是否新增類名後缀
     * @param  string $common       公共模組名
     * @return object
     * @throws ClassNotFoundException
     */
    public static function model($name = '', $layer = 'model', $appendSuffix = false, $common = 'common')
    {
        $uid = $name . $layer;

        if (isset(self::$instance[$uid])) {
            return self::$instance[$uid];
        }

        list($module, $class) = self::getModuleAndClass($name, $layer, $appendSuffix);

        if (class_exists($class)) {
            $model = new $class();
        } else {
            $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);

            if (class_exists($class)) {
                $model = new $class();
            } else {
                throw new ClassNotFoundException('class not exists:' . $class, $class);
            }
        }

        return self::$instance[$uid] = $model;
    }

    /**
     * 實例化（分層）控制器 格式：[模組名/]控制器名
     * @access public
     * @param  string $name         資源地址
     * @param  string $layer        控制層名稱
     * @param  bool   $appendSuffix 是否新增類名後缀
     * @param  string $empty        空控制器名稱
     * @return object
     * @throws ClassNotFoundException
     */
    public static function controller($name, $layer = 'controller', $appendSuffix = false, $empty = '')
    {
        list($module, $class) = self::getModuleAndClass($name, $layer, $appendSuffix);

        if (class_exists($class)) {
            return App::invokeClass($class);
        }

        if ($empty) {
            $emptyClass = self::parseClass($module, $layer, $empty, $appendSuffix);

            if (class_exists($emptyClass)) {
                return new $emptyClass(Request::instance());
            }
        }

        throw new ClassNotFoundException('class not exists:' . $class, $class);
    }

    /**
     * 實例化驗證類 格式：[模組名/]驗證器名
     * @access public
     * @param  string $name         資源地址
     * @param  string $layer        驗證層名稱
     * @param  bool   $appendSuffix 是否新增類名後缀
     * @param  string $common       公共模組名
     * @return object|false
     * @throws ClassNotFoundException
     */
    public static function validate($name = '', $layer = 'validate', $appendSuffix = false, $common = 'common')
    {
        $name = $name ?: Config::get('default_validate');

        if (empty($name)) {
            return new Validate;
        }

        $uid = $name . $layer;
        if (isset(self::$instance[$uid])) {
            return self::$instance[$uid];
        }

        list($module, $class) = self::getModuleAndClass($name, $layer, $appendSuffix);

        if (class_exists($class)) {
            $validate = new $class;
        } else {
            $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);

            if (class_exists($class)) {
                $validate = new $class;
            } else {
                throw new ClassNotFoundException('class not exists:' . $class, $class);
            }
        }

        return self::$instance[$uid] = $validate;
    }

    /**
     * 解析模組和類名
     * @access protected
     * @param  string $name         資源地址
     * @param  string $layer        驗證層名稱
     * @param  bool   $appendSuffix 是否新增類名後缀
     * @return array
     */
    protected static function getModuleAndClass($name, $layer, $appendSuffix)
    {
        if (false !== strpos($name, '\\')) {
            $module = Request::instance()->module();
            $class  = $name;
        } else {
            if (strpos($name, '/')) {
                list($module, $name) = explode('/', $name, 2);
            } else {
                $module = Request::instance()->module();
            }

            $class = self::parseClass($module, $layer, $name, $appendSuffix);
        }

        return [$module, $class];
    }

    /**
     * 資料庫初始化 並取得資料庫類實例
     * @access public
     * @param  mixed       $config 資料庫配置
     * @param  bool|string $name   連結標識 true 强制重新連結
     * @return \think\db\Connection
     */
    public static function db($config = [], $name = false)
    {
        return Db::connect($config, $name);
    }

    /**
     * 远程调用模組的操作方法 参數格式 [模組/控制器/]操作
     * @access public
     * @param  string       $url          调用地址
     * @param  string|array $vars         调用参數 支援字符串和數组
     * @param  string       $layer        要调用的控制層名稱
     * @param  bool         $appendSuffix 是否新增類名後缀
     * @return mixed
     */
    public static function action($url, $vars = [], $layer = 'controller', $appendSuffix = false)
    {
        $info   = pathinfo($url);
        $action = $info['basename'];
        $module = '.' != $info['dirname'] ? $info['dirname'] : Request::instance()->controller();
        $class  = self::controller($module, $layer, $appendSuffix);

        if ($class) {
            if (is_scalar($vars)) {
                if (strpos($vars, '=')) {
                    parse_str($vars, $vars);
                } else {
                    $vars = [$vars];
                }
            }

            return App::invokeMethod([$class, $action . Config::get('action_suffix')], $vars);
        }

        return false;
    }

    /**
     * 字符串命名風格轉換
     * type 0 将 Java 風格轉換為 C 的風格 1 将 C 風格轉換為 Java 的風格
     * @access public
     * @param  string  $name    字符串
     * @param  integer $type    轉換類型
     * @param  bool    $ucfirst 首字母是否大寫（駝峰規則）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);

            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }

    /**
     * 解析應用類的類名
     * @access public
     * @param  string $module       模組名
     * @param  string $layer        層名 controller model ...
     * @param  string $name         類名
     * @param  bool   $appendSuffix 是否新增類名後缀
     * @return string
     */
    public static function parseClass($module, $layer, $name, $appendSuffix = false)
    {

        $array = explode('\\', str_replace(['/', '.'], '\\', $name));
        $class = self::parseName(array_pop($array), 1);
        $class = $class . (App::$suffix || $appendSuffix ? ucfirst($layer) : '');
        $path  = $array ? implode('\\', $array) . '\\' : '';

        return App::$namespace . '\\' .
            ($module ? $module . '\\' : '') .
            $layer . '\\' . $path . $class;
    }

    /**
     * 初始化類的實例
     * @access public
     * @return void
     */
    public static function clearInstance()
    {
        self::$instance = [];
    }
}

// 作用範圍隔離

/**
 * include
 * @param  string $file 文件路徑
 * @return mixed
 */
function __include_file($file)
{
    return include $file;
}

/**
 * require
 * @param  string $file 文件路徑
 * @return mixed
 */
function __require_file($file)
{
    return require $file;
}
