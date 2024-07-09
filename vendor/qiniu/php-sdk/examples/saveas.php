<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;

// 後台来取得AK, SK
$accessKey = 'Access_Key';
$secretKey = 'Secret_Key';

//產生EncodedEntryURI的值
$entry = '<bucket>:<Key>';//<Key>為產生缩略圖的文件名
//產生的值
$encodedEntryURI = \Qiniu\base64_urlSafeEncode($entry);

//使用SecretKey對新的下载URL进行HMAC1-SHA1签名
$newurl = "78re52.com1.z0.glb.clouddn.com/resource/Ship.jpg?imageView2/2/w/200/h/200|saveas/" . $encodedEntryURI;

$sign = hash_hmac("sha1", $newurl, $secretKey, true);

//對签名进行URL安全的Base64编碼
$encodedSign = \Qiniu\base64_urlSafeEncode($sign);
//最终得到的完整下载URL
$finalURL = "http://" . $newurl . "/sign/" . $accessKey . ":" . $encodedSign;

$callbackBody = file_get_contents("$finalURL");

echo $callbackBody;
