<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\sae;

use think\Exception;

class Template
{
    // mc 對象
    private $mc;
    // 编译快取内容
    private $contents = [];

    /**
     * 架构函數
     * @access public
     */
    public function __construct()
    {
        if (!function_exists('sae_debug')) {
            throw new Exception('請在SAE平台上运行程式碼。');
        }
        $this->mc = new \Memcached();
        if (!$this->mc) {
            throw new Exception('您未開通Memcache服务，請在SAE管理平台初始化Memcache服务');
        }
    }

    /**
     * 寫入编译快取
     * @param string $cacheFile 快取的文件名
     * @param string $content 快取的内容
     * @return void|array
     */
    public function write($cacheFile, $content)
    {
        // 新增寫入時間
        $content = $_SERVER['REQUEST_TIME'] . $content;
        if (!$this->mc->set($cacheFile, $content, 0)) {
            throw new Exception('sae mc write error:' . $cacheFile);
        } else {
            $this->contents[$cacheFile] = $content;
            return true;
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
        if (!empty($vars) && is_array($vars)) {
            extract($vars, EXTR_OVERWRITE);
        }
        eval('?>' . $this->get($cacheFile, 'content'));
    }

    /**
     * 檢查编译快取是否有效
     * @param string  $cacheFile 快取的文件名
     * @param int     $cacheTime 快取時間
     * @return boolean
     */
    public function check($cacheFile, $cacheTime)
    {
        $mtime = $this->get($cacheFile, 'mtime');
        if (0 != $cacheTime && $_SERVER['REQUEST_TIME'] > $mtime + $cacheTime) {
            // 快取是否在有效期
            return false;
        }
        return true;
    }

    /**
     * 读取文件訊息
     * @access private
     * @param string $filename  文件名
     * @param string $name  訊息名 mtime或者content
     * @return boolean
     */
    private function get($filename, $name)
    {
        if (!isset($this->contents[$filename])) {
            $this->contents[$filename] = $this->mc->get($filename);
        }
        $content = $this->contents[$filename];

        if (false === $content) {
            return false;
        }
        $info = array(
            'mtime'   => substr($content, 0, 10),
            'content' => substr($content, 10),
        );
        return $info[$name];
    }
}
