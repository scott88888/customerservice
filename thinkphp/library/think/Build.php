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

class Build
{
    /**
     * 根據傳入的 build 资料建立目錄和文件
     * @access public
     * @param  array  $build     build 列表
     * @param  string $namespace 應用類庫命名空間
     * @param  bool   $suffix    類庫後缀
     * @return void
     * @throws Exception
     */
    public static function run(array $build = [], $namespace = 'app', $suffix = false)
    {
        // 锁定
        $lock = APP_PATH . 'build.lock';

        // 如果锁定文件不可寫(不存在)則进行處理，否則表示已经有程序在處理了
        if (!is_writable($lock)) {
            if (!touch($lock)) {
                throw new Exception(
                    '應用目錄[' . APP_PATH . ']不可寫，目錄無法自動產生！<BR>請手動產生项目目錄~',
                    10006
                );
            }

            foreach ($build as $module => $list) {
                if ('__dir__' == $module) {
                    // 建立目錄列表
                    self::buildDir($list);
                } elseif ('__file__' == $module) {
                    // 建立文件列表
                    self::buildFile($list);
                } else {
                    // 建立模組
                    self::module($module, $list, $namespace, $suffix);
                }
            }

            // 解除锁定
            unlink($lock);
        }
    }

    /**
     * 建立目錄
     * @access protected
     * @param  array $list 目錄列表
     * @return void
     */
    protected static function buildDir($list)
    {
        foreach ($list as $dir) {
            // 目錄不存在則建立目錄
            !is_dir(APP_PATH . $dir) && mkdir(APP_PATH . $dir, 0755, true);
        }
    }

    /**
     * 建立文件
     * @access protected
     * @param  array $list 文件列表
     * @return void
     */
    protected static function buildFile($list)
    {
        foreach ($list as $file) {
            // 先建立目錄
            if (!is_dir(APP_PATH . dirname($file))) {
                mkdir(APP_PATH . dirname($file), 0755, true);
            }

            // 再建立文件
            if (!is_file(APP_PATH . $file)) {
                file_put_contents(
                    APP_PATH . $file,
                    'php' == pathinfo($file, PATHINFO_EXTENSION) ? "<?php\n" : ''
                );
            }
        }
    }

    /**
     * 建立模組
     * @access public
     * @param  string $module    模組名
     * @param  array  $list      build 列表
     * @param  string $namespace 應用類庫命名空間
     * @param  bool   $suffix    類庫後缀
     * @return void
     */
    public static function module($module = '', $list = [], $namespace = 'app', $suffix = false)
    {
        $module = $module ?: '';

        // 建立模組目錄
        !is_dir(APP_PATH . $module) && mkdir(APP_PATH . $module);

        // 如果不是 runtime 目錄則需要建立配置文件和公共文件、建立模組的默認頁面
        if (basename(RUNTIME_PATH) != $module) {
            self::buildCommon($module);
            self::buildHello($module, $namespace, $suffix);
        }

        // 未指定文件和目錄，則建立默認的模組目錄和文件
        if (empty($list)) {
            $list = [
                '__file__' => ['config.php', 'common.php'],
                '__dir__'  => ['controller', 'model', 'view'],
            ];
        }

        // 建立子目錄和文件
        foreach ($list as $path => $file) {
            $modulePath = APP_PATH . $module . DS;

            if ('__dir__' == $path) {
                // 產生子目錄
                foreach ($file as $dir) {
                    self::checkDirBuild($modulePath . $dir);
                }
            } elseif ('__file__' == $path) {
                // 產生（空白）文件
                foreach ($file as $name) {
                    if (!is_file($modulePath . $name)) {
                        file_put_contents(
                            $modulePath . $name,
                            'php' == pathinfo($name, PATHINFO_EXTENSION) ? "<?php\n" : ''
                        );
                    }
                }
            } else {
                // 產生相關 MVC 文件
                foreach ($file as $val) {
                    $val      = trim($val);
                    $filename = $modulePath . $path . DS . $val . ($suffix ? ucfirst($path) : '') . EXT;
                    $space    = $namespace . '\\' . ($module ? $module . '\\' : '') . $path;
                    $class    = $val . ($suffix ? ucfirst($path) : '');

                    switch ($path) {
                        case 'controller': // 控制器
                            $content = "<?php\nnamespace {$space};\n\nclass {$class}\n{\n\n}";
                            break;
                        case 'model': // 模型
                            $content = "<?php\nnamespace {$space};\n\nuse think\Model;\n\nclass {$class} extends Model\n{\n\n}";
                            break;
                        case 'view': // 视圖
                            $filename = $modulePath . $path . DS . $val . '.html';
                            self::checkDirBuild(dirname($filename));
                            $content = '';
                            break;
                        default:
                            // 其他文件
                            $content = "<?php\nnamespace {$space};\n\nclass {$class}\n{\n\n}";
                    }

                    if (!is_file($filename)) {
                        file_put_contents($filename, $content);
                    }
                }
            }
        }
    }

    /**
     * 建立模組的欢迎頁面
     * @access protected
     * @param  string $module    模組名
     * @param  string $namespace 應用類庫命名空間
     * @param  bool   $suffix    類庫後缀
     * @return void
     */
    protected static function buildHello($module, $namespace, $suffix = false)
    {
        $filename = APP_PATH . ($module ? $module . DS : '') .
            'controller' . DS . 'Index' .
            ($suffix ? 'Controller' : '') . EXT;

        if (!is_file($filename)) {
            $module = $module ? $module . '\\' : '';
            $suffix = $suffix ? 'Controller' : '';
            $content = str_replace(
                ['{$app}', '{$module}', '{layer}', '{$suffix}'],
                [$namespace, $module, 'controller', $suffix],
                file_get_contents(THINK_PATH . 'tpl' . DS . 'default_index.tpl')
            );

            self::checkDirBuild(dirname($filename));
            file_put_contents($filename, $content);
        }
    }

    /**
     * 建立模組的公共文件
     * @access protected
     * @param  string $module 模組名
     * @return void
     */
    protected static function buildCommon($module)
    {
        $config = CONF_PATH . ($module ? $module . DS : '') . 'config.php';

        self::checkDirBuild(dirname($config));

        if (!is_file($config)) {
            file_put_contents($config, "<?php\n//配置文件\nreturn [\n\n];");
        }

        $common = APP_PATH . ($module ? $module . DS : '') . 'common.php';
        if (!is_file($common)) file_put_contents($common, "<?php\n");
    }

    /**
     * 建立目錄
     * @access protected
     * @param  string $dirname 目錄名稱
     * @return void
     */
    protected static function checkDirBuild($dirname)
    {
        !is_dir($dirname) && mkdir($dirname, 0755, true);
    }
}
