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

namespace think\cache\driver;

use think\cache\Driver;

/**
 * Xcache快取驱動
 * @author    liu21st <liu21st@gmail.com>
 */
class Xcache extends Driver
{
    protected $options = [
        'prefix' => '',
        'expire' => 0,
    ];

    /**
     * 构造函數
     * @param array $options 快取参數
     * @access public
     * @throws \BadFunctionCallException
     */
    public function __construct($options = [])
    {
        if (!function_exists('xcache_info')) {
            throw new \BadFunctionCallException('not support: Xcache');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * 判断快取
     * @access public
     * @param string $name 快取变量名
     * @return bool
     */
    public function has($name)
    {
        $key = $this->getCacheKey($name);
        return xcache_isset($key);
    }

    /**
     * 读取快取
     * @access public
     * @param string $name 快取变量名
     * @param mixed  $default 默認值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $key = $this->getCacheKey($name);
        return xcache_isset($key) ? xcache_get($key) : $default;
    }

    /**
     * 寫入快取
     * @access public
     * @param string            $name 快取变量名
     * @param mixed             $value  存储資料
     * @param integer|\DateTime $expire  有效時間（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
        if ($this->tag && !$this->has($name)) {
            $first = true;
        }
        $key = $this->getCacheKey($name);
        if (xcache_set($key, $value, $expire)) {
            isset($first) && $this->setTagItem($key);
            return true;
        }
        return false;
    }

    /**
     * 自增快取（針對數值快取）
     * @access public
     * @param string    $name 快取变量名
     * @param int       $step 步長
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        $key = $this->getCacheKey($name);
        return xcache_inc($key, $step);
    }

    /**
     * 自减快取（針對數值快取）
     * @access public
     * @param string    $name 快取变量名
     * @param int       $step 步長
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        $key = $this->getCacheKey($name);
        return xcache_dec($key, $step);
    }

    /**
     * 刪除快取
     * @access public
     * @param string $name 快取变量名
     * @return boolean
     */
    public function rm($name)
    {
        return xcache_unset($this->getCacheKey($name));
    }

    /**
     * 清除快取
     * @access public
     * @param string $tag 标签名
     * @return boolean
     */
    public function clear($tag = null)
    {
        if ($tag) {
            // 指定标签清除
            $keys = $this->getTagItem($tag);
            foreach ($keys as $key) {
                xcache_unset($key);
            }
            $this->rm('tag_' . md5($tag));
            return true;
        }
        if (function_exists('xcache_unset_by_prefix')) {
            return xcache_unset_by_prefix($this->options['prefix']);
        } else {
            return false;
        }
    }
}
