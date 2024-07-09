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
 * Wincache快取驱動
 * @author    liu21st <liu21st@gmail.com>
 */
class Wincache extends Driver
{
    protected $options = [
        'prefix' => '',
        'expire' => 0,
    ];

    /**
     * 构造函數
     * @param array $options 快取参數
     * @throws \BadFunctionCallException
     * @access public
     */
    public function __construct($options = [])
    {
        if (!function_exists('wincache_ucache_info')) {
            throw new \BadFunctionCallException('not support: WinCache');
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
        return wincache_ucache_exists($key);
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
        return wincache_ucache_exists($key) ? wincache_ucache_get($key) : $default;
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
        $key = $this->getCacheKey($name);
        if ($this->tag && !$this->has($name)) {
            $first = true;
        }
        if (wincache_ucache_set($key, $value, $expire)) {
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
        return wincache_ucache_inc($key, $step);
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
        return wincache_ucache_dec($key, $step);
    }

    /**
     * 刪除快取
     * @access public
     * @param string $name 快取变量名
     * @return boolean
     */
    public function rm($name)
    {
        return wincache_ucache_delete($this->getCacheKey($name));
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
            $keys = $this->getTagItem($tag);
            foreach ($keys as $key) {
                wincache_ucache_delete($key);
            }
            $this->rm('tag_' . md5($tag));
            return true;
        } else {
            return wincache_ucache_clear();
        }
    }

}
