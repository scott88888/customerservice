<?php

require dirname(__FILE__) . '/../vendor/autoload.php';

$secretId = "COS_SECRETID"; //"云 API 密钥 SecretId";
$secretKey = "COS_SECRETKEY"; //"云 API 密钥 SecretKey";
$region = "ap-beijing"; //設定一个默认的存储桶地域
$cosClient = new Qcloud\Cos\Client(
    array(
        'region' => $region,
        'schema' => 'https', //协议头部，默认為http
        'credentials'=> array(
            'secretId'  => $secretId ,
            'secretKey' => $secretKey)));
try {
    $result = $cosClient->copyObject(array(
        'Bucket' => 'examplebucket-125000000', //格式：BucketName-APPID
        'Key' => 'exampleobject',
        'CopySource' => 'examplebucket2-125000000.cos.ap-guangzhou.myqcloud.com/exampleobject',
        'MetadataDirective' => 'Replaced',
    ));
    // 請求成功
    print_r($result);
} catch (\Exception $e) {
    // 請求失敗
    echo($e);
}

