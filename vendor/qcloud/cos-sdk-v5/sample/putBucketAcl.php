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
    $result = $cosClient->putBucketAcl(array(
        //bucket的命名規則為{name}-{appid} ，此处填寫的存储桶名稱必须為此格式
        'Bucket' => 'examplebucket-125000000',
        'ACL' => 'private',
        'Grants' => array(
            array(
                'Grantee' => array(
                    'DisplayName' => 'qcs::cam::uin/100000000001:uin/100000000001',
                    'ID' => 'qcs::cam::uin/100000000001:uin/100000000001',
                    'Type' => 'CanonicalUser',
                ),
                'Permission' => 'FULL_CONTROL',
            ),
            // ... repeated
        ),
        'Owner' => array(
            'DisplayName' => 'qcs::cam::uin/3210232098:uin/3210232098',
            'ID' => 'qcs::cam::uin/3210232098:uin/3210232098',
        )));
    // 請求成功
    print_r($result);
} catch (\Exception $e) {
    // 請求失敗
    echo "$e\n";
}

