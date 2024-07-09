<?php

require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;

$accessKey = 'Access_Key';
$secretKey = 'Secret_Key';
$auth = new Auth($accessKey, $secretKey);

// 要转碼的文件所在的空間。
$bucket = 'Bucket_Name';

// 转碼是使用的對列名稱。 https://portal.qiniu.com/mps/pipeline
$pipeline = 'pipeline_name';

// 初始化
$pfop = new PersistentFop($auth, $bucket, $pipeline);
