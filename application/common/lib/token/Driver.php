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

namespace app\common\lib\token;

/**
 * Token基础類
 */
abstract class Driver
{
    protected $handler = null;
    protected $options = [];

    /**
     * 存储Token
     * @param   string $token Token
     * @param   int $user_id 会员ID
     * @param   int $expire 过期时長,0表示無限,單位秒
     * @return bool
     */
    abstract function set($token, $user_id, $expire = 0);

    /**
     * 取得Token内的訊息
     * @param   string $token
     * @return  array
     */
    abstract function get($token);

    /**
     * 判断Token是否可用
     * @param   string $token Token
     * @param   int $user_id 会员ID
     * @return  boolean
     */
    abstract function check($token, $user_id);

    /**
     * 刪除Token
     * @param   string $token
     * @return  boolean
     */
    abstract function delete($token);

    /**
     * 刪除指定使用者的所有Token
     * @param   int $user_id
     * @return  boolean
     */
    abstract function clear($user_id);

    /**
     * 返回句柄對象，可執行其它高级方法
     *
     * @access public
     * @return object
     */
    public function handler()
    {
        return $this->handler;
    }

    /**
     * 取得加密後的Token
     * @param string $token Token標識
     * @return string
     */
    protected function getEncryptedToken($token)
    {
        $config = \think\Config::get('token');
        return hash_hmac($config['hashalgo'], $token,AIKF_SALT);
    }

    /**
     * 取得过期剩余时長
     * @param $expiretime
     * @return float|int|mixed
     */
    protected function getExpiredIn($expiretime)
    {
        return $expiretime ? max(0, $expiretime - time()) : 365 * 86400;
    }
}
