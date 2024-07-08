<?php

require dirname(__FILE__) . '/../vendor/autoload.php';

$secretId = "COS_SECRETID"; //"云 API 密钥 SecretId";
$secretKey = "COS_SECRETKEY"; //"云 API 密钥 SecretKey";
$region = "ap-beijing"; //设置一个默认的存储桶地域
$cosClient = new Qcloud\Cos\Client(
    array(
        'region' => $region,
        'schema' => 'https', //协议头部，默认为http
        'credentials'=> array(
            'secretId'  => $secretId ,
            'secretKey' => $secretKey)));
try {
    $result = $cosClient->getBucketAcl(array(
        'Bucket' => 'examplebucket-125000000' //格式：BucketName-APPID
    ));
    // 请求成功
    print_r($result);
} catch (\Exception $e) {
    // 请求失敗
    $statusCode = $e->getStatusCode(); // 取得错误码
    $errorMessage = $e->getMessage(); // 取得错误信息
    $requestId = $e->getRequestId(); // 取得错误的requestId
    $errorCode = $e->getCosErrorCode(); // 取得错误名稱
    $request = $e->getRequest(); // 取得完整的请求
    $response = $e->getResponse(); // 取得完整的响应
}

