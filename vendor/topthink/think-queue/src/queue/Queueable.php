<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think\queue;

trait Queueable
{

    /** @var string 對列名稱 */
    public $queue;

    /** @var integer 延迟時間 */
    public $delay;

    /**
     * 設定對列名
     * @param $queue
     * @return $this
     */
    public function queue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * 設定延迟時間
     * @param $delay
     * @return $this
     */
    public function delay($delay)
    {
        $this->delay = $delay;

        return $this;
    }
}