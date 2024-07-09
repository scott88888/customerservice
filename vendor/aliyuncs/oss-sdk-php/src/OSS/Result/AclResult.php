<?php

namespace OSS\Result;

use OSS\Core\OssException;

/**
 * Class AclResult getBucketAcl接口返回结果類，封装了
 * 返回的xml資料的解析
 *
 * @package OSS\Result
 */
class AclResult extends Result
{
    /**
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
        if (isset($xml->AccessControlList->Grant)) {
            return strval($xml->AccessControlList->Grant);
        } else {
            throw new OssException("xml format exception");
        }
    }
}