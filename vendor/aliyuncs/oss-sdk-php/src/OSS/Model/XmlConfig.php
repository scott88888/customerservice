<?php

namespace OSS\Model;

/**
 * Interface XmlConfig
 * @package OSS\Model
 */
interface XmlConfig
{

    /**
     * 接口定义，實現此接口的类都需要實現从xml資料解析的函數
     *
     * @param string $strXml
     * @return null
     */
    public function parseFromXml($strXml);

    /**
     * 接口定义，實現此接口的类，都需要實現把子类序列化成xml字符串的接口
     *
     * @return string
     */
    public function serializeToXml();

}
