<?php

namespace OSS\Result;

use OSS\Core\OssException;

/**
 * Class AclResult getBucketAcl接口返回结果類，封装了
 * 返回的xml資料的解析
 *
 * @package OSS\Result
 */
class GetStorageCapacityResult extends Result
{
    /**
     * Parse data from response
     * 
     * @return string
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }
        $xml = simplexml_load_string($content);
        if (isset($xml->StorageCapacity)) {
            return intval($xml->StorageCapacity);
        } else {
            throw new OssException("xml format exception");
        }
    }
}