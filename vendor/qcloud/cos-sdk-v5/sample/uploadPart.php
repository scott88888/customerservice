<?php

require dirname(__FILE__) . '/../vendor/autoload.php';

$secretId = "COS_SECRETID"; //"云 API 密钥 SecretId";
$secretKey = "COS_SECRETKEY"; //"云 API 密钥 SecretKey";
$region = "ap-beijing"; //設定一个默認的存储桶地域
$cosClient = new Qcloud\Cos\Client(
    array(
        'region' => $region,
        'schema' => 'https', //协议头部，默認為http
        'credentials'=> array(
            'secretId'  => $secretId ,
            'secretKey' => $secretKey)));
try {
    $result = $cosClient->uploadPart(array(
        'Bucket' => 'examplebucket-125000000', //格式：BucketName-APPID
        'Key' => 'exampleobject', 
        'Body' => 'string',
        'UploadId' => 'NWNhNDY0YzFfMmZiNTM1MGFfNTM2YV8xYjliMTg',
        'PartNumber' => 1,
        'ContentMD5' => 'string',
        'ContentLength' => 100,
    ));
    // 請求成功
    print_r($result);
} catch (\Exception $e) {
    // 請求失敗
    echo($e);
}

