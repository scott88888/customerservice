<?php

require_once __DIR__ . '/../autoload.php';

use \Qiniu\Cdn\CdnManager;

$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');

$auth = new Qiniu\Auth($accessKey, $secretKey);

//待刷新的文件列表和目錄，文件列表最多一次100个，目錄最多一次10个
//参考文档：http://developer.qiniu.com/article/fusion/api/refresh.html
$urls = array(
    "http://phpsdk.qiniudn.com/qiniu.jpg",
    "http://phpsdk.qiniudn.com/qiniu2.jpg",
);

//刷新目錄需要联系七牛技術支援開通账户权限
$dirs = array(
    "http://phpsdk.qiniudn.com/test/"
);

$cdnManager = new CdnManager($auth);

// 目前客户默認没有目錄刷新权限，刷新会有400038报错，参考：https://developer.qiniu.com/fusion/api/1229/cache-refresh
// 需要刷新目錄請工單联系技術支援 https://support.qiniu.com/tickets/category
list($refreshResult, $refreshErr) = $cdnManager->refreshUrlsAndDirs($urls, $dirs);
if ($refreshErr != null) {
    var_dump($refreshErr);
} else {
    echo "refresh request sent\n";
    print_r($refreshResult);
}

//如果只有刷新連結或者目錄的需求，可以分布使用

list($refreshResult, $refreshErr) = $cdnManager->refreshUrls($urls);
if ($refreshErr != null) {
    var_dump($refreshErr);
} else {
    echo "refresh request sent\n";
    print_r($refreshResult);
}

list($refreshResult, $refreshErr) = $cdnManager->refreshDirs($dirs);
if ($refreshErr != null) {
    var_dump($refreshErr);
} else {
    echo "refresh request sent\n";
    print_r($refreshResult);
}
