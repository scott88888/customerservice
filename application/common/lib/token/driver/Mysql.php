<?php

namespace app\common\lib\token\driver;

use app\common\lib\token\Driver;

/**
 * Token操作类
 */
class Mysql extends Driver
{

    /**
     * 默认配置
     * @var array
     */
    protected $options = [
        'table'      => 'wolive_admin_token',
        'expire'     => 2592000,
        'connection' => [],
    ];


    /**
     * 构造函數
     * @param array $options 参數
     * @access public
     */
    public function __construct($options = [])
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        if ($this->options['connection']) {
            $this->handler = \think\Db::connect($this->options['connection'])->name($this->options['table']);
        } else {
            $this->handler = \think\Db::name($this->options['table']);
        }
    }

    /**
     * 存储Token
     * @param   string $token Token
     * @param   int $user_id 会员ID
     * @param   int $expire 过期时長,0表示無限,單位秒
     * @return bool
     */
    public function set($token, $user_id, $expire = null)
    {
        $expiretime = !is_null($expire) && $expire !== 0 ? time() + $expire : 0;
        $token = $this->getEncryptedToken($token);
        $this->handler->insert(['token' => $token, 'user_id' => $user_id, 'createtime' => time(), 'expiretime' => $expiretime]);
        return TRUE;
    }

    /**
     * 取得Token内的訊息
     * @param   string $token
     * @return  array
     */
    public function get($token)
    {
        $data = $this->handler->where('token', $this->getEncryptedToken($token))->find();
        if ($data) {
            if (!$data['expiretime'] || $data['expiretime'] > time()) {
                //返回未加密的token给客户端使用
                $data['token'] = $token;
                //返回剩余有效時間
                $data['expires_in'] = $this->getExpiredIn($data['expiretime']);
                return $data;
            } else {
                self::delete($token);
            }
        }
        return [];
    }

    /**
     * 判断Token是否可用
     * @param   string $token Token
     * @param   int $user_id 会员ID
     * @return  boolean
     */
    public function check($token, $user_id)
    {
        $data = $this->get($token);
        return $data && $data['user_id'] == $user_id ? true : false;
    }

    /**
     * 刪除Token
     * @param   string $token
     * @return  boolean
     */
    public function delete($token)
    {
        $this->handler->where('token', $this->getEncryptedToken($token))->delete();
        return true;
    }

    /**
     * 刪除指定使用者的所有Token
     * @param   int $user_id
     * @return  boolean
     */
    public function clear($user_id)
    {
        $this->handler->where('user_id', $user_id)->delete();
        return true;
    }

}
