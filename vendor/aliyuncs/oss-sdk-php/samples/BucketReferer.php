<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use \OSS\Model\RefererConfig;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* 简單使用 ****************************************************************

//設定referer白名單
$refererConfig = new RefererConfig();
$refererConfig->setAllowEmptyReferer(true);
$refererConfig->addReferer("www.aliiyun.com");
$refererConfig->addReferer("www.aliiyuncs.com");
$ossClient->putBucketReferer($bucket, $refererConfig);
Common::println("bucket $bucket refererConfig created:" . $refererConfig->serializeToXml());
//取得Referer白名單
$refererConfig = $ossClient->getBucketReferer($bucket);
Common::println("bucket $bucket refererConfig fetched:" . $refererConfig->serializeToXml());

//刪除referer白名單
$refererConfig = new RefererConfig();
$ossClient->putBucketReferer($bucket, $refererConfig);
Common::println("bucket $bucket refererConfig deleted");


//******************************* 完整用法参考下面函數 ****************************************************

putBucketReferer($ossClient, $bucket);
getBucketReferer($ossClient, $bucket);
deleteBucketReferer($ossClient, $bucket);
getBucketReferer($ossClient, $bucket);

/**
 * 設定bucket的防盗链配置
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function putBucketReferer($ossClient, $bucket)
{
    $refererConfig = new RefererConfig();
    $refererConfig->setAllowEmptyReferer(true);
    $refererConfig->addReferer("www.aliiyun.com");
    $refererConfig->addReferer("www.aliiyuncs.com");
    try {
        $ossClient->putBucketReferer($bucket, $refererConfig);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * 取得bucket的防盗链配置
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function getBucketReferer($ossClient, $bucket)
{
    $refererConfig = null;
    try {
        $refererConfig = $ossClient->getBucketReferer($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($refererConfig->serializeToXml() . "\n");
}

/**
 * 刪除bucket的防盗链配置
 * Referer白名單不能直接清空，只能通過重新設定来覆盖之前的規則。
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function deleteBucketReferer($ossClient, $bucket)
{
    $refererConfig = new RefererConfig();
    try {
        $ossClient->putBucketReferer($bucket, $refererConfig);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
