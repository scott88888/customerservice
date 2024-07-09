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
$local_path = "/data/exampleobject";
try {
    $result = $cosClient->upload(
        $bucket = 'examplebucket-125000000', //格式：BucketName-APPID
        $key = 'exampleobject',
        $body = fopen($local_path, 'rb')
        /*
        $options = array(
            'ACL' => 'string',
            'CacheControl' => 'string',
            'ContentDisposition' => 'string',
            'ContentEncoding' => 'string',
            'ContentLanguage' => 'string',
            'ContentLength' => integer,
            'ContentType' => 'string',
            'Expires' => 'string',
            'GrantFullControl' => 'string',
            'GrantRead' => 'string',
            'GrantWrite' => 'string',
            'Metadata' => array(
                'string' => 'string',
            ),
            'ContentMD5' => 'string',
            'ServerSideEncryption' => 'string',
            'StorageClass' => 'string'
        )
        */
    );
    // 請求成功
    print_r($result);
} catch (\Exception $e) {
    // 請求失敗
    echo($e);
}

