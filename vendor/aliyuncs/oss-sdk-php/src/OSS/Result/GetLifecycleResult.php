<?php

namespace OSS\Result;


use OSS\Model\LifecycleConfig;

/**
 * Class GetLifecycleResult
 * @package OSS\Result
 */
class GetLifecycleResult extends Result
{
    /**
     *  解析Lifestyle資料
     *
     * @return LifecycleConfig
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new LifecycleConfig();
        $config->parseFromXml($content);
        return $config;
    }

    /**
     * 根據返回http狀態碼判断，[200-299]即认為是OK, 取得bucket相關配置的接口，404也认為是一种
     * 有效响应
     *
     * @return bool
     */
    protected function isResponseOk()
    {
        $status = $this->rawResponse->status;
        if ((int)(intval($status) / 100) == 2 || (int)(intval($status)) === 404) {
            return true;
        }
        return false;
    }
}