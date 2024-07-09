<?php
require_once __DIR__ . '/../autoload.php';

use \Qiniu\Auth;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$bucket = getenv('QINIU_TEST_BUCKET');

// 初始化Auth狀態
$auth = new Auth($accessKey, $secretKey);

// 简單上傳凭证
$expires = 3600;

$policy = null;
$upToken = $auth->uploadToken($bucket, null, $expires, $policy, true);
print($upToken . "\n");

// 自訂凭证有效期（示例2小时）
$expires = 7200;
$upToken = $auth->uploadToken($bucket, null, $expires, $policy, true);
print($upToken . "\n");

// 覆盖上傳凭证
$expires = 3600;
$keyToOverwrite = 'qiniu.mp4';
$upToken = $auth->uploadToken($bucket, $keyToOverwrite, $expires, $policy, true);
print($upToken . "\n");

//自訂上傳回覆（非callback模式）凭证
$returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"bucket":"$(bucket)","name":"$(x:name)"}';
$policy = array(
    'returnBody' => $returnBody
);
$upToken = $auth->uploadToken($bucket, null, $expires, $policy, true);
print($upToken . "\n");

//带回调業務服务器的凭证（application/json）
$policy = array(
    'callbackUrl' => 'http://api.example.com/qiniu/upload/callback',
    'callbackBody' => '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"bucket":"$(bucket)","name":"$(x:name)"}',
    'callbackBodyType' => 'application/json'
);
$upToken = $auth->uploadToken($bucket, null, $expires, $policy, true);
print($upToken . "\n");


//带回调業務服务器的凭证（application/x-www-form-urlencoded）
$policy = array(
    'callbackUrl' => 'http://api.example.com/qiniu/upload/callback',
    'callbackBody' => 'key=$(key)&hash=$(etag)&bucket=$(bucket)&fsize=$(fsize)&name=$(x:name)'
);
$upToken = $auth->uploadToken($bucket, null, $expires, $policy, true);
print($upToken . "\n");

//带資料處理的凭证
$saveMp4Entry = \Qiniu\base64_urlSafeEncode($bucket . ":avthumb_test_target.mp4");
$saveJpgEntry = \Qiniu\base64_urlSafeEncode($bucket . ":vframe_test_target.jpg");
$avthumbMp4Fop = "avthumb/mp4|saveas/" . $saveMp4Entry;
$vframeJpgFop = "vframe/jpg/offset/1|saveas/" . $saveJpgEntry;
$policy = array(
    'persistentOps' => $avthumbMp4Fop . ";" . $vframeJpgFop,
    'persistentPipeline' => "video-pipe",
    'persistentNotifyUrl' => "http://api.example.com/qiniu/pfop/notify",
);
$upToken = $auth->uploadToken($bucket, null, $expires, $policy, true);
print($upToken . "\n");
