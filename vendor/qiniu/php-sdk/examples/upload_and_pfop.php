<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$bucket = getenv('QINIU_TEST_BUCKET');
$auth = new Auth($accessKey, $secretKey);


// 在七牛保存的文件名
$key = 'php-logo.png';
$uploadMgr = new UploadManager();

$pfop = "imageMogr2/rotate/90|saveas/" . \Qiniu\base64_urlSafeEncode($bucket . ":php-logo-rotate.png");

//转碼完成後通知到你的業務服务器。（公網可以訪問，並相应200 OK）
$notifyUrl = 'http://notify.fake.com';

//独立的转碼對列：https://portal.qiniu.com/mps/pipeline
$pipeline = 'sdktest';

$policy = array(
    'persistentOps' => $pfop,
    'persistentNotifyUrl' => $notifyUrl,
    'persistentPipeline' => $pipeline
);
$token = $auth->uploadToken($bucket, null, 3600, $policy);

list($ret, $err) = $uploadMgr->putFile($token, null, $key);
echo "\n====> putFile result: \n";
if ($err !== null) {
    var_dump($err);
} else {
    var_dump($ret);
}
