<?php

namespace OSS\Model;


/**
 * Bucket訊息，ListBuckets接口返回資料
 *
 * Class BucketInfo
 * @package OSS\Model
 */
class BucketInfo
{
    /**
     * BucketInfo constructor.
     *
     * @param string $location
     * @param string $name
     * @param string $createDate
     */
    public function __construct($location, $name, $createDate)
    {
        $this->location = $location;
        $this->name = $name;
        $this->createDate = $createDate;
    }

    /**
     * 得到bucket所在的region
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * 得到bucket的名稱
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 得到bucket的建立時間
     *
     * @return string
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * bucket所在的region
     *
     * @var string
     */
    private $location;
    /**
     * bucket的名稱
     *
     * @var string
     */
    private $name;

    /**
     * bucket的建立事件
     *
     * @var string
     */
    private $createDate;

}