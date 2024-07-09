<?php
namespace app\common\lib;

/**
 * cURL請求工具類
 */
class CurlUtils {

    private $ch;//curl資源對象

    /**
     * 构造方法
     * @param string $url 請求的地址
     * @param int $responseHeader 是否需要响应头訊息
     */
    public function __construct($url,$responseHeader = 0,$timeout = 5){
        $this->ch = curl_init($url);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,1);//設定以文件流的形式返回
        curl_setopt($this->ch,CURLOPT_HEADER,$responseHeader);//設定响应头訊息是否返回
        curl_setopt($this->ch,CURLOPT_TIMEOUT,$timeout);//設定响应头訊息是否返回
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); //不驗證证书
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false); //不驗證证书
    }

    /**
     * 析构方法
     */
    public function __destruct(){
        $this->close();
    }

    /**
     * 新增請求头
     * @param array $value 請求头
     */
    public function addHeader($value){
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $value);
    }

    /**
     * 發送請求
     * @return string 返回的資料
     */
    private function exec(){
        return curl_exec($this->ch);
    }

    /**
     * 發送get請求
     * @return string 請求返回的資料
     */
    public function get(){
        return $this->exec();
    }

    /**
     * 發送post請求
     * @param  arr/string $value 准备發送post的資料
     * @param boolean $https 是否為https請求
     * @return string        請求返回的資料
     */
    public function post($value,$https=true){
        if($https){
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($this->ch,CURLOPT_POST,1);//設定post請求
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,$value);
        return $this->exec();
    }

    /**
     * 關閉curl句柄
     */
    private function close(){
        curl_close($this->ch);
    }

}