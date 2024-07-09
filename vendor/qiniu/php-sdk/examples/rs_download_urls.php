<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

// 构建Auth對象
$auth = new Auth($accessKey, $secretKey);

// 私有空間中的外链 http://<domain>/<file_key>
$baseUrl = 'http://if-pri.qiniudn.com/qiniu.png?imageView2/1/h/500';
// 對連結进行签名
$signedUrl = $auth->privateDownloadUrl($baseUrl);

echo $signedUrl;
