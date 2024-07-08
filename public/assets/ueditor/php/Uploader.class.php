<?php

use app\common\lib\Storage;

/**
 * UEditor編輯器通用上传类
 */
class Uploader
{
    private $rid; //存储标识
    private $watermark; //是否图片水印
    private $attachment_info; //附件存储信息
    private $attachment_model; //附件模型
    private $fileMd5; //文件md5

    private $fileField; //文件域名
    private $file; //文件上传对象
    private $base64; //文件上传对象
    private $config; //配置信息
    private $oriName; //原始文件名
    private $fileName; //新文件名
    private $fullName; //完整文件名,即从当前配置目录开始的URL
    private $filePath; //完整文件名,即从当前配置目录开始的URL
    private $fileUrl; //完整文件URL
    private $fileSize; //文件大小
    private $fileType; //文件类型
    private $stateInfo; //上传狀態信息,
    private $stateMap = array( //上传狀態映射表，国际化使用者需考虑此处数据的国际化
        "SUCCESS", //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制",
        "文件大小超出 MAX_FILE_SIZE 限制",
        "文件未被完整上传",
        "没有文件被上传",
        "上传文件为空",
        "ERROR_TMP_FILE" => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED" => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED" => "文件类型不允许",
        "ERROR_CREATE_DIR" => "目录创建失敗",
        "ERROR_DIR_NOT_WRITEABLE" => "目录没有写权限",
        "ERROR_FILE_MOVE" => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND" => "找不到上传文件",
        "ERROR_WRITE_CONTENT" => "写入文件内容错误",
        "ERROR_UNKNOWN" => "未知错误",
        "ERROR_DEAD_LINK" => "連結不可用",
        "ERROR_HTTP_LINK" => "連結不是http連結",
        "ERROR_HTTP_CONTENTTYPE" => "連結contentType不正确",
        "INVALID_URL" => "非法 URL",
        "INVALID_IP" => "非法 IP"
    );

    /**
     * 构造函数
     * @param string $fileField 表單名稱
     * @param array $config 配置项
     * @param bool $base64 是否解析base64编码，可省略。若開啟，则$fileField代表的是base64编码的字符串表單名
     */
    public function __construct($fileField, $config, $type = "upload")
    {
        $this->fileField = $fileField;
        $this->config = $config;
        $this->type = $type;
        if ($type == "remote") {
            $this->saveRemote();
        } else if($type == "base64") {
            $this->upBase64();
        } else {
            $this->upFile();
        }

        //$this->stateMap['ERROR_TYPE_NOT_ALLOWED'] = iconv('unicode', 'utf-8', $this->stateMap['ERROR_TYPE_NOT_ALLOWED']);
    }

    /**
     * 上传文件的主处理方法
     * @return mixed
     */
    private function upFile()
    {
        $file = $this->file = $_FILES[$this->fileField];
        if (!$file) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            return;
        } elseif (!file_exists($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMP_FILE_NOT_FOUND");
            return;
        } else if (!is_uploaded_file($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMPFILE");
            return;
        } elseif ($this->file['error']) {
            $this->stateInfo = $this->getStateInfo($file['error']);
            return;
        }

        $this->oriName = $file['name'];
        $this->fileSize = $file['size'];
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();

        //檢查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }
        //檢查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo("ERROR_TYPE_NOT_ALLOWED");
            return;
        }
$this->fileMd5=md5_file($file["tmp_name"]);
        Storage::$variable = $this->fileField;
        $url = Storage::put();
        $this->fileUrl = $url['url'];
        $this->stateInfo = $this->stateMap[0];
        // 存储附件
        $this->save_attach();
    }

    /**
     * 处理base64编码的图片上传
     * @return mixed
     */
    private function upBase64()
    {
        $base64Data = $_POST[$this->fileField];
        $img = base64_decode($base64Data);

        $this->oriName = $this->config['oriName'];
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();

        //檢查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        // 安全检测
        $rt = \Phpcmf\Service::L('upload')->_safe_check(trim($this->getFileExt(), '.'), $img, 0);
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        $rt = \Phpcmf\Service::L('upload')->save_file(
            'content',
            $img,
            $this->fullName,
            $this->attachment_info,
            $this->watermark
        );
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        $this->fileUrl = $this->attachment_info['url'].$this->fullName;
        $this->stateInfo = $this->stateMap[0];

        // 存储附件
        $this->save_attach($rt);
    }

    /**
     * 拉取远程图片
     * @return mixed
     */
    private function saveRemote()
    {
        $imgUrl = htmlspecialchars($this->fileField);
        $imgUrl = str_replace("&amp;", "&", $imgUrl);

        //http开头驗證
        if (strpos($imgUrl, "http") !== 0) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_LINK");
            return;
        }

        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            $this->stateInfo = $this->getStateInfo("INVALID_URL");
            return;
        }

        preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
        $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

        // 此时提取出来的可能是 ip 也有可能是域名，先取得 ip
        $ip = gethostbyname($host_without_protocol);
        // 判断是否是私有 ip
        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            $this->stateInfo = $this->getStateInfo("INVALID_IP");
            return;
        }

        //取得请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            $this->stateInfo = $this->getStateInfo("ERROR_DEAD_LINK");
            return;
        }
        //格式驗證(扩展名驗證和Content-Type驗證)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $this->config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_CONTENTTYPE");
            return;
        }

        //打开输出缓冲区并取得远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);

        $this->oriName = $m ? $m[1]:"";
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();

        //檢查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        // 安全检测
        $rt = \Phpcmf\Service::L('upload')->_safe_check(trim($this->getFileExt(), '.'), $img, 0);
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        $rt = \Phpcmf\Service::L('upload')->save_file(
            'content',
            $img,
            $this->fullName,
            $this->attachment_info,
            $this->watermark
        );
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        $this->fileUrl = $this->attachment_info['url'].$this->fullName;
        $this->stateInfo = $this->stateMap[0];

        // 存储附件
        $this->save_attach($rt);
    }

    /**
     * 上传错误檢查
     * @param $errCode
     * @return string
     */
    private function getStateInfo($errCode)
    {
        return !$this->stateMap[$errCode] ? '上传错误('.$errCode.')' : $this->stateMap[$errCode];
    }

    /**
     * 取得文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        return strtolower(strrchr($this->oriName, '.'));
    }

    /**
     * 重命名文件
     * @return string
     */
    private function getFullName()
    {
        //替换日期事件
        $t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $this->config["pathFormat"];
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);

        //过滤文件名的非法自负,并替换文件名
        $oriName = substr($this->oriName, 0, strrpos($this->oriName, '.'));
        $oriName = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/", '', $oriName);
        $format = str_replace("{filename}", $oriName, $format);

        //替换随机字符串
        $randNum = substr(md5(time().$oriName), rand(0, 20), 15); // 随机新名字
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }

        $ext = $this->getFileExt();
        $this->fileName = str_replace($ext, '', $oriName);

        return trim($format . $ext, '/');
    }

    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType()
    {
        return in_array($this->getFileExt(), $this->config["allowFiles"]);
    }

    /**
     * 文件大小检测
     * @return bool
     */
    private function  checkSize()
    {
        return $this->fileSize <= ($this->config["maxSize"]);
    }

    /**
     * 存储归档
     * @return bool
     */
    private function save_attach()
    {
        db('wolive_attachment_data')->insert([
            'service_id' => input('param.service_id',0),
            'admin_id'=>input('param.admin_id',0),
            'filename'=>$this->oriName,
            'filesize'=>$this->fileSize,
            'fileext'=>trim($this->fileType, '.'),
            'url'=>$this->fileUrl,
            'filemd5'=>$this->fileMd5,
            'inputtime'=>time()
        ]);
    }

    /**
     * 取得当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        $title = strstr($this->oriName, '.', true);
        if ($_GET['action'] == 'uploadimage' && in_array($this->fileType, ['.jpg', '.jpeg', '.gif', '.png'])) {
             // 图片属性
            if (isset($this->config['imageAltValue']) && $this->config['imageAltValue'] == 'name') {

            } else {
                $title = 'www.laikephp.com';
            }
        }
        return [
            "state" => $this->stateInfo,
            "url" => $this->fileUrl,
            "title" => $title,
            "original" => $title,
            "type" => $this->fileType,
            "size" => $this->fileSize
        ];
    }

}