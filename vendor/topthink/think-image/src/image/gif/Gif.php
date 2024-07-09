<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think\image\gif;

class Gif
{
    /**
     * GIF帧列表
     *
     * @var array
     */
    private $frames = [];
    /**
     * 每帧等待时间列表
     *
     * @var array
     */
    private $delays = [];

    /**
     * 构造方法，用于解码GIF图片
     *
     * @param string $src GIF图片資料
     * @param string $mod 图片資料类型
     * @throws \Exception
     */
    public function __construct($src = null, $mod = 'url')
    {
        if (!is_null($src)) {
            if ('url' == $mod && is_file($src)) {
                $src = file_get_contents($src);
            }
            /* 解码GIF图片 */
            try {
                $de           = new Decoder($src);
                $this->frames = $de->getFrames();
                $this->delays = $de->getDelays();
            } catch (\Exception $e) {
                throw new \Exception("解码GIF图片出错");
            }
        }
    }

    /**
     * 设置或取得当前帧的資料
     *
     * @param  string $stream 二进制資料流
     * @return mixed        取得到的資料
     */
    public function image($stream = null)
    {
        if (is_null($stream)) {
            $current = current($this->frames);
            return false === $current ? reset($this->frames) : $current;
        }
        $this->frames[key($this->frames)] = $stream;
    }

    /**
     * 将当前帧移动到下一帧
     *
     * @return string 当前帧資料
     */
    public function nextImage()
    {
        return next($this->frames);
    }

    /**
     * 编码并保存当前GIF图片
     *
     * @param  string $pathname 图片名稱
     */
    public function save($pathname)
    {
        $gif = new Encoder($this->frames, $this->delays, 0, 2, 0, 0, 0, 'bin');
        file_put_contents($pathname, $gif->getAnimation());
    }
}