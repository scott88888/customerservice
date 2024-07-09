<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;

$accessKey = 'Access_Key';
$secretKey = 'Secret_Key';
$auth = new Auth($accessKey, $secretKey);

//要持久化處理的文件所在的空間和文件名。
$bucket = 'Bucket_Name';

//持久化處理使用的對列名稱。 https://portal.qiniu.com/mps/pipeline
$pipeline = 'pipeline_name';

//持久化處理完成後通知到你的業務服务器。
$notifyUrl = 'http://375dec79.ngrok.com/notify.php';
$pfop = new PersistentFop($auth, $bucket, $pipeline, $notifyUrl);

$id = "z2.5955c739e3d0041bf80c9baa";
//查詢持久化處理的进度和狀態
list($ret, $err) = $pfop->status($id);
echo "\n====> pfop avthumb status: \n";
if ($err != null) {
    var_dump($err);
} else {
    var_dump($ret);
}
