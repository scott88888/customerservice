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

namespace think\response;

use think\Collection;
use think\Model;
use think\Response;

class Xml extends Response
{
    // 输出参數
    protected $options = [
        // 根节點名
        'root_node' => 'think',
        // 根节點属性
        'root_attr' => '',
        //數字索引的子节點名
        'item_node' => 'item',
        // 數字索引子节點key转换的属性名
        'item_key'  => 'id',
        // 資料编碼
        'encoding'  => 'utf-8',
    ];

    protected $contentType = 'text/xml';

    /**
     * 处理資料
     * @access protected
     * @param mixed $data 要处理的資料
     * @return mixed
     */
    protected function output($data)
    {
        // XML資料转换
        return $this->xmlEncode($data, $this->options['root_node'], $this->options['item_node'], $this->options['root_attr'], $this->options['item_key'], $this->options['encoding']);
    }

    /**
     * XML编碼
     * @param mixed $data 資料
     * @param string $root 根节點名
     * @param string $item 數字索引的子节點名
     * @param string $attr 根节點属性
     * @param string $id   數字索引子节點key转换的属性名
     * @param string $encoding 資料编碼
     * @return string
     */
    protected function xmlEncode($data, $root, $item, $attr, $id, $encoding)
    {
        if (is_array($attr)) {
            $array = [];
            foreach ($attr as $key => $value) {
                $array[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $array);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml  = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml .= "<{$root}{$attr}>";
        $xml .= $this->dataToXml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }

    /**
     * 資料XML编碼
     * @param mixed  $data 資料
     * @param string $item 數字索引时的节點名稱
     * @param string $id   數字索引key转换為的属性名
     * @return string
     */
    protected function dataToXml($data, $item, $id)
    {
        $xml = $attr = '';

        if ($data instanceof Collection || $data instanceof Model) {
            $data = $data->toArray();
        }

        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key         = $item;
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= (is_array($val) || is_object($val)) ? $this->dataToXml($val, $item, $id) : $val;
            $xml .= "</{$key}>";
        }
        return $xml;
    }
}
