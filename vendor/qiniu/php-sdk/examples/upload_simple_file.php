<?php
require_once __DIR__ . '/../autoload.php';

// 引入鉴权類
use Qiniu\Auth;

// 引入上傳類
use Qiniu\Storage\UploadManager;

// 需要填寫你的 Access Key 和 Secret Key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$bucket = getenv('QINIU_TEST_BUCKET');

// 构建鉴权對象
$auth = new Auth($accessKey, $secretKey);

// 產生上傳 Token
$token = $auth->uploadToken($bucket);

// 要上傳文件的本地路徑
$filePath = './php-logo.png';

// 上傳到七牛後保存的文件名
$key = 'my-php-logo.png';

// 初始化 UploadManager 對象並进行文件的上傳。
$uploadMgr = new UploadManager();

// 调用 UploadManager 的 putFile 方法进行文件的上傳。
list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
echo "\n====> putFile result: \n";
if ($err !== null) {
    var_dump($err);
} else {
    var_dump($ret);
}
