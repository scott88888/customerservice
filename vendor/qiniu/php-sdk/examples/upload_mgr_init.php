<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

$accessKey = 'Access_Key';
$secretKey = 'Secret_Key';
$auth = new Auth($accessKey, $secretKey);

// 空間名  http://developer.qiniu.com/docs/v6/api/overview/concepts.html#bucket
$bucket = 'Bucket_Name';

// 產生上传Token
$token = $auth->uploadToken($bucket);

// 构建 UploadManager 對象
$uploadMgr = new UploadManager();
