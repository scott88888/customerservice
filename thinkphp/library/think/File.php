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

namespace think;

use SplFileObject;

class File extends SplFileObject
{
    /**
     * @var string 錯誤訊息
     */
    private $error = '';

    /**
     * @var string 當前完整文件名
     */
    protected $filename;

    /**
     * @var string 上傳文件名
     */
    protected $saveName;

    /**
     * @var string 文件上傳命名規則
     */
    protected $rule = 'date';

    /**
     * @var array 文件上傳驗證規則
     */
    protected $validate = [];

    /**
     * @var bool 單元测试
     */
    protected $isTest;

    /**
     * @var array 上傳文件訊息
     */
    protected $info;

    /**
     * @var array 文件 hash 訊息
     */
    protected $hash = [];

    /**
     * File constructor.
     * @access public
     * @param  string $filename 文件名稱
     * @param  string $mode     訪問模式
     */
    public function __construct($filename, $mode = 'r')
    {
        parent::__construct($filename, $mode);
        $this->filename = $this->getRealPath() ?: $this->getPathname();
    }

    /**
     * 設定是否是單元测试
     * @access public
     * @param  bool $test 是否是测试
     * @return $this
     */
    public function isTest($test = false)
    {
        $this->isTest = $test;

        return $this;
    }

    /**
     * 設定上傳訊息
     * @access public
     * @param  array $info 上傳文件訊息
     * @return $this
     */
    public function setUploadInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * 取得上傳文件的訊息
     * @access public
     * @param  string $name 訊息名稱
     * @return array|string
     */
    public function getInfo($name = '')
    {
        return isset($this->info[$name]) ? $this->info[$name] : $this->info;
    }

    /**
     * 取得上傳文件的文件名
     * @access public
     * @return string
     */
    public function getSaveName()
    {
        return $this->saveName;
    }

    /**
     * 設定上傳文件的保存文件名
     * @access public
     * @param  string $saveName 保存名稱
     * @return $this
     */
    public function setSaveName($saveName)
    {
        $this->saveName = $saveName;

        return $this;
    }

    /**
     * 取得文件的哈希散列值
     * @access public
     * @param  string $type 類型
     * @return string
     */
    public function hash($type = 'sha1')
    {
        if (!isset($this->hash[$type])) {
            $this->hash[$type] = hash_file($type, $this->filename);
        }

        return $this->hash[$type];
    }

    /**
     * 檢查目錄是否可寫
     * @access protected
     * @param  string $path 目錄
     * @return boolean
     */
    protected function checkPath($path)
    {
        if (is_dir($path) || mkdir($path, 0755, true)) {
            return true;
        }

        $this->error = ['directory {:path} creation failed', ['path' => $path]];

        return false;
    }

    /**
     * 取得文件類型訊息
     * @access public
     * @return string
     */
    public function getMime()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        return finfo_file($finfo, $this->filename);
    }

    /**
     * 設定文件的命名規則
     * @access public
     * @param  string $rule 文件命名規則
     * @return $this
     */
    public function rule($rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * 設定上傳文件的驗證規則
     * @access public
     * @param  array $rule 驗證規則
     * @return $this
     */
    public function validate(array $rule = [])
    {
        $this->validate = $rule;

        return $this;
    }

    /**
     * 检测是否合法的上傳文件
     * @access public
     * @return bool
     */
    public function isValid()
    {
        return $this->isTest ? is_file($this->filename) : is_uploaded_file($this->filename);
    }

    /**
     * 检测上傳文件
     * @access public
     * @param  array $rule 驗證規則
     * @return bool
     */
    public function check($rule = [])
    {
        $rule = $rule ?: $this->validate;

        /* 檢查文件大小 */
        if (isset($rule['size']) && !$this->checkSize($rule['size'])) {
            $this->error = 'filesize not match';
            return false;
        }

        /* 檢查文件 Mime 類型 */
        if (isset($rule['type']) && !$this->checkMime($rule['type'])) {
            $this->error = 'mimetype to upload is not allowed';
            return false;
        }

        /* 檢查文件後缀 */
        if (isset($rule['ext']) && !$this->checkExt($rule['ext'])) {
            $this->error = 'extensions to upload is not allowed';
            return false;
        }

        /* 檢查圖像文件 */
        if (!$this->checkImg()) {
            $this->error = 'illegal image files';
            return false;
        }

        return true;
    }

    /**
     * 检测上傳文件後缀
     * @access public
     * @param  array|string $ext 允许後缀
     * @return bool
     */
    public function checkExt($ext)
    {
        if (is_string($ext)) {
            $ext = explode(',', $ext);
        }

        $extension = strtolower(pathinfo($this->getInfo('name'), PATHINFO_EXTENSION));

        return in_array($extension, $ext);
    }

    /**
     * 检测圖像文件
     * @access public
     * @return bool
     */
    public function checkImg()
    {
        $extension = strtolower(pathinfo($this->getInfo('name'), PATHINFO_EXTENSION));

        // 如果上傳的不是圖片，或者是圖片而且後缀确实符合圖片類型則返回 true
        return !in_array($extension, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf']) || in_array($this->getImageType($this->filename), [1, 2, 3, 4, 6, 13]);
    }

    /**
     * 判断圖像類型
     * @access protected
     * @param  string $image 圖片名稱
     * @return bool|int
     */
    protected function getImageType($image)
    {
        if (function_exists('exif_imagetype')) {
            return exif_imagetype($image);
        }

        try {
            $info = getimagesize($image);
            return $info ? $info[2] : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 检测上傳文件大小
     * @access public
     * @param  integer $size 最大大小
     * @return bool
     */
    public function checkSize($size)
    {
        return $this->getSize() <= $size;
    }

    /**
     * 检测上傳文件類型
     * @access public
     * @param  array|string $mime 允许類型
     * @return bool
     */
    public function checkMime($mime)
    {
        $mime = is_string($mime) ? explode(',', $mime) : $mime;

        return in_array(strtolower($this->getMime()), $mime);
    }

    /**
     * 移動文件
     * @access public
     * @param  string      $path     保存路徑
     * @param  string|bool $savename 保存的文件名 默認自動產生
     * @param  boolean     $replace  同名文件是否覆盖
     * @return false|File
     */
    public function move($path, $savename = true, $replace = true)
    {
        // 文件上傳失敗，捕获錯誤程式碼
        if (!empty($this->info['error'])) {
            $this->error($this->info['error']);
            return false;
        }

        // 检测合法性
        if (!$this->isValid()) {
            $this->error = 'upload illegal files';
            return false;
        }

        // 驗證上傳
        if (!$this->check()) {
            return false;
        }

        $path = rtrim($path, DS) . DS;
        // 文件保存命名規則
        $saveName = $this->buildSaveName($savename);
        $filename = $path . $saveName;

        // 检测目錄
        if (false === $this->checkPath(dirname($filename))) {
            return false;
        }

        // 不覆盖同名文件
        if (!$replace && is_file($filename)) {
            $this->error = ['has the same filename: {:filename}', ['filename' => $filename]];
            return false;
        }

        /* 移動文件 */
        if ($this->isTest) {
            rename($this->filename, $filename);
        } elseif (!move_uploaded_file($this->filename, $filename)) {
            $this->error = 'upload write error';
            return false;
        }

        // 返回 File 對象實例
        $file = new self($filename);
        $file->setSaveName($saveName)->setUploadInfo($this->info);

        return $file;
    }

    /**
     * 取得保存文件名
     * @access protected
     * @param  string|bool $savename 保存的文件名 默認自動產生
     * @return string
     */
    protected function buildSaveName($savename)
    {
        // 自動產生文件名
        if (true === $savename) {
            if ($this->rule instanceof \Closure) {
                $savename = call_user_func_array($this->rule, [$this]);
            } else {
                switch ($this->rule) {
                    case 'date':
                        $savename = date('Ymd') . DS . md5(microtime(true));
                        break;
                    default:
                        if (in_array($this->rule, hash_algos())) {
                            $hash     = $this->hash($this->rule);
                            $savename = substr($hash, 0, 2) . DS . substr($hash, 2);
                        } elseif (is_callable($this->rule)) {
                            $savename = call_user_func($this->rule);
                        } else {
                            $savename = date('Ymd') . DS . md5(microtime(true));
                        }
                }
            }
        } elseif ('' === $savename || false === $savename) {
            $savename = $this->getInfo('name');
        }

        if (!strpos($savename, '.')) {
            $savename .= '.' . pathinfo($this->getInfo('name'), PATHINFO_EXTENSION);
        }

        return $savename;
    }

    /**
     * 取得錯誤程式碼訊息
     * @access private
     * @param  int $errorNo 錯誤号
     * @return $this
     */
    private function error($errorNo)
    {
        switch ($errorNo) {
            case 1:
            case 2:
                $this->error = 'upload File size exceeds the maximum value';
                break;
            case 3:
                $this->error = 'only the portion of file is uploaded';
                break;
            case 4:
                $this->error = 'no file to uploaded';
                break;
            case 6:
                $this->error = 'upload temp dir not found';
                break;
            case 7:
                $this->error = 'file write error';
                break;
            default:
                $this->error = 'unknown upload error';
        }

        return $this;
    }

    /**
     * 取得錯誤訊息（支援多語言）
     * @access public
     * @return string
     */
    public function getError()
    {
        if (is_array($this->error)) {
            list($msg, $vars) = $this->error;
        } else {
            $msg  = $this->error;
            $vars = [];
        }

        return Lang::has($msg) ? Lang::get($msg, $vars) : $msg;
    }

    /**
     * 魔法方法，取得文件的 hash 值
     * @access public
     * @param  string $method 方法名
     * @param  mixed  $args   调用参數
     * @return string
     */
    public function __call($method, $args)
    {
        return $this->hash($method);
    }
}
