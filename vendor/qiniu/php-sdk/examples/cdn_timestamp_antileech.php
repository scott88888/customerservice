<?php

require_once __DIR__ . '/../autoload.php';

use \Qiniu\Cdn\CdnManager;

//建立時間戳防盗链
//時間戳防盗链密钥，后台取得
$encryptKey = 'your_domain_timestamp_antileech_encryptkey';

//带訪問协议的域名
$url1 = 'http://phpsdk.qiniuts.com/24.jpg?avinfo';
$url2 = 'http://phpsdk.qiniuts.com/24.jpg';

//有效期時間（單位秒）
$durationInSeconds = 3600;

$signedUrl = CdnManager::createTimestampAntiLeechUrl($url1, $encryptKey, $durationInSeconds);
print($signedUrl);
