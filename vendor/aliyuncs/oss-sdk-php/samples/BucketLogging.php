<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//*******************************简單使用***************************************************************

// 設定Bucket訪問日志记录规则, 訪問日志文件的存放位置是同bucket下的access.log前缀的文件
$ossClient->putBucketLogging($bucket, $bucket, "access.log", array());
Common::println("bucket $bucket lifecycleConfig created");

// 取得Bucket訪問日志记录规则
$loggingConfig = $ossClient->getBucketLogging($bucket, array());
Common::println("bucket $bucket lifecycleConfig fetched:" . $loggingConfig->serializeToXml());

// 刪除Bucket訪問日志记录规则
$loggingConfig = $ossClient->getBucketLogging($bucket, array());
Common::println("bucket $bucket lifecycleConfig deleted");

//******************************* 完整用法参考下面函數 ****************************************************

putBucketLogging($ossClient, $bucket);
getBucketLogging($ossClient, $bucket);
deleteBucketLogging($ossClient, $bucket);
getBucketLogging($ossClient, $bucket);

/**
 * 設定bucket的Logging配置
 *
 * @param OssClient $ossClient OssClient实例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function putBucketLogging($ossClient, $bucket)
{
    $option = array();
    //訪問日志存放在本bucket下
    $targetBucket = $bucket;
    $targetPrefix = "access.log";

    try {
        $ossClient->putBucketLogging($bucket, $targetBucket, $targetPrefix, $option);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * 取得bucket的Logging配置
 *
 * @param OssClient $ossClient OssClient实例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function getBucketLogging($ossClient, $bucket)
{
    $loggingConfig = null;
    $options = array();
    try {
        $loggingConfig = $ossClient->getBucketLogging($bucket, $options);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($loggingConfig->serializeToXml() . "\n");
}

/**
 * 刪除bucket的Logging配置
 *
 * @param OssClient $ossClient OssClient实例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function deleteBucketLogging($ossClient, $bucket)
{
    try {
        $ossClient->deleteBucketLogging($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
