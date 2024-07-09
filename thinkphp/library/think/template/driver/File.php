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

namespace think\template\driver;

use think\Exception;

class File
{
    protected $cacheFile;

    /**
     * 寫入编译快取
     * @param string $cacheFile 快取的文件名
     * @param string $content 快取的内容
     * @return void|array
     */
    public function write($cacheFile, $content)
    {
        // 检测模板目錄
        $dir = dirname($cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        // 產生模板快取文件
        if (false === file_put_contents($cacheFile, $content)) {
            throw new Exception('cache write error:' . $cacheFile, 11602);
        }
    }

    /**
     * 读取编译编译
     * @param string  $cacheFile 快取的文件名
     * @param array   $vars 变量數组
     * @return void
     */
    public function read($cacheFile, $vars = [])
    {
        $this->cacheFile = $cacheFile;
        if (!empty($vars) && is_array($vars)) {
            // 模板阵列变量分解成為独立变量
            extract($vars, EXTR_OVERWRITE);
        }
        //载入模版快取文件
        include $this->cacheFile;
    }

    /**
     * 檢查编译快取是否有效
     * @param string  $cacheFile 快取的文件名
     * @param int     $cacheTime 快取時間
     * @return boolean
     */
    public function check($cacheFile, $cacheTime)
    {
        // 快取文件不存在, 直接返回false
        if (!file_exists($cacheFile)) {
            return false;
        }
        if (0 != $cacheTime && $_SERVER['REQUEST_TIME'] > filemtime($cacheFile) + $cacheTime) {
            // 快取是否在有效期
            return false;
        }
        return true;
    }
}
