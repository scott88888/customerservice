<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;

//對已经上傳到七牛的视频发起异步转碼操作

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$bucket = getenv('QINIU_TEST_BUCKET');

$auth = new Auth($accessKey, $secretKey);

//要转碼的文件所在的空間和文件名。
$key = 'qiniu.mp4';

//转碼是使用的對列名稱。 https://portal.qiniu.com/mps/pipeline
$pipeline = 'sdktest';
$force = false;

//转碼完成後通知到你的業務服务器。
$notifyUrl = 'http://375dec79.ngrok.com/notify.php';
$config = new \Qiniu\Config();
//$config->useHTTPS=true;

$pfop = new PersistentFop($auth, $config);

//要进行转碼的转碼操作。 http://developer.qiniu.com/docs/v6/api/reference/fop/av/avthumb.html
$fops = "avthumb/mp4/s/640x360/vb/1.4m|saveas/" . \Qiniu\base64_urlSafeEncode($bucket . ":qiniu_640x360.mp4");

list($id, $err) = $pfop->execute($bucket, $key, $fops, $pipeline, $notifyUrl, $force);
echo "\n====> pfop avthumb result: \n";
if ($err != null) {
    var_dump($err);
} else {
    echo "PersistentFop Id: $id\n";
}

//查詢转碼的进度和狀態
list($ret, $err) = $pfop->status($id);
echo "\n====> pfop avthumb status: \n";
if ($err != null) {
    var_dump($err);
} else {
    var_dump($ret);
}
