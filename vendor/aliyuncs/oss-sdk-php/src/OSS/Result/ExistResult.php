<?php

namespace OSS\Result;

/**
 * Class ExistResult 檢查bucket和object是否存在的返回结果，
 * 根據返回response的http status判断
 * @package OSS\Result
 */
class ExistResult extends Result
{
    /**
     * @return bool
     */
    protected function parseDataFromResponse()
    {
        return intval($this->rawResponse->status) === 200 ? true : false;
    }

    /**
     * 根據返回http狀態碼判断，[200-299]即认為是OK, 判断是否存在的接口，404也认為是一种
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