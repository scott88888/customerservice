<?php

namespace OSS\Model;

/**
 * Class PrefixInfo
 *
 * listObjects接口中返回的Prefix列表中的類
 * listObjects接口返回資料中包含两个Array:
 * 一个是拿到的Object列表【可以理解成對应文件系统中的文件列表】
 * 一个是拿到的Prefix列表【可以理解成對应文件系统中的目錄列表】
 *
 * @package OSS\Model
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/GetBucket.html
 */
class PrefixInfo
{
    /**
     * PrefixInfo constructor.
     * @param string $prefix
     */
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    private $prefix;
}