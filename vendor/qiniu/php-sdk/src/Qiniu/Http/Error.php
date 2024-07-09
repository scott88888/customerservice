<?php
namespace Qiniu\Http;

/**
 * 七牛业务請求逻辑錯誤封装类，主要用来解析API請求返回如下的内容：
 * <pre>
 *     {"error" : "detailed error message"}
 * </pre>
 */
final class Error
{
    private $url;
    private $response;

    public function __construct($url, $response)
    {
        $this->url = $url;
        $this->response = $response;
    }

    public function code()
    {
        return $this->response->statusCode;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function message()
    {
        return $this->response->error;
    }
}
