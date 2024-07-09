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
 * Sqlite快取驱動
 * @author    liu21st <liu21st@gmail.com>
 */
class Sqlite extends Driver
{
    protected $options = [
        'db'         => ':memory:',
        'table'      => 'sharedmemory',
        'prefix'     => '',
        'expire'     => 0,
        'persistent' => false,
    ];

    /**
     * 构造函數
     * @param array $options 快取参數
     * @throws \BadFunctionCallException
     * @access public
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('sqlite')) {
            throw new \BadFunctionCallException('not support: sqlite');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $func          = $this->options['persistent'] ? 'sqlite_popen' : 'sqlite_open';
        $this->handler = $func($this->options['db']);
    }

    /**
     * 取得实际的快取標識
     * @access public
     * @param string $name 快取名
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $this->options['prefix'] . sqlite_escape_string($name);
    }

    /**
     * 判断快取
     * @access public
     * @param string $name 快取变量名
     * @return bool
     */
    public function has($name)
    {
        $name   = $this->getCacheKey($name);
        $sql    = 'SELECT value FROM ' . $this->options['table'] . ' WHERE var=\'' . $name . '\' AND (expire=0 OR expire >' . $_SERVER['REQUEST_TIME'] . ') LIMIT 1';
        $result = sqlite_query($this->handler, $sql);
        return sqlite_num_rows($result);
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
        $name   = $this->getCacheKey($name);
        $sql    = 'SELECT value FROM ' . $this->options['table'] . ' WHERE var=\'' . $name . '\' AND (expire=0 OR expire >' . $_SERVER['REQUEST_TIME'] . ') LIMIT 1';
        $result = sqlite_query($this->handler, $sql);
        if (sqlite_num_rows($result)) {
            $content = sqlite_fetch_single($result);
            if (function_exists('gzcompress')) {
                //启用資料压缩
                $content = gzuncompress($content);
            }
            return unserialize($content);
        }
        return $default;
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
        $name  = $this->getCacheKey($name);
        $value = sqlite_escape_string(serialize($value));
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp();
        } else {
            $expire = (0 == $expire) ? 0 : (time() + $expire); //快取有效期為0表示永久快取
        }
        if (function_exists('gzcompress')) {
            //資料压缩
            $value = gzcompress($value, 3);
        }
        if ($this->tag) {
            $tag       = $this->tag;
            $this->tag = null;
        } else {
            $tag = '';
        }
        $sql = 'REPLACE INTO ' . $this->options['table'] . ' (var, value, expire, tag) VALUES (\'' . $name . '\', \'' . $value . '\', \'' . $expire . '\', \'' . $tag . '\')';
        if (sqlite_query($this->handler, $sql)) {
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
        if ($this->has($name)) {
            $value = $this->get($name) + $step;
        } else {
            $value = $step;
        }
        return $this->set($name, $value, 0) ? $value : false;
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
        if ($this->has($name)) {
            $value = $this->get($name) - $step;
        } else {
            $value = -$step;
        }
        return $this->set($name, $value, 0) ? $value : false;
    }

    /**
     * 刪除快取
     * @access public
     * @param string $name 快取变量名
     * @return boolean
     */
    public function rm($name)
    {
        $name = $this->getCacheKey($name);
        $sql  = 'DELETE FROM ' . $this->options['table'] . ' WHERE var=\'' . $name . '\'';
        sqlite_query($this->handler, $sql);
        return true;
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
            $name = sqlite_escape_string($tag);
            $sql  = 'DELETE FROM ' . $this->options['table'] . ' WHERE tag=\'' . $name . '\'';
            sqlite_query($this->handler, $sql);
            return true;
        }
        $sql = 'DELETE FROM ' . $this->options['table'];
        sqlite_query($this->handler, $sql);
        return true;
    }
}
