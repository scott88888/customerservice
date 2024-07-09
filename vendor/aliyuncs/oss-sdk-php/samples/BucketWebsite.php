<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\WebsiteConfig;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//*******************************简單使用***************************************************************

// 設定Bucket的静态網站托管模式
$websiteConfig = new WebsiteConfig("index.html", "error.html");
$ossClient->putBucketWebsite($bucket, $websiteConfig);
Common::println("bucket $bucket websiteConfig created:" . $websiteConfig->serializeToXml());

// 查看Bucket的静态網站托管狀態
$websiteConfig = $ossClient->getBucketWebsite($bucket);
Common::println("bucket $bucket websiteConfig fetched:" . $websiteConfig->serializeToXml());

// 刪除Bucket的静态網站托管模式
$ossClient->deleteBucketWebsite($bucket);
Common::println("bucket $bucket websiteConfig deleted");

//******************************* 完整用法参考下面函數 ****************************************************

putBucketWebsite($ossClient, $bucket);
getBucketWebsite($ossClient, $bucket);
deleteBucketWebsite($ossClient, $bucket);
getBucketWebsite($ossClient, $bucket);

/**
 * 設定bucket的静态網站托管模式配置
 *
 * @param $ossClient OssClient
 * @param  $bucket string 存储空間名稱
 * @return null
 */
function putBucketWebsite($ossClient, $bucket)
{
    $websiteConfig = new WebsiteConfig("index.html", "error.html");
    try {
        $ossClient->putBucketWebsite($bucket, $websiteConfig);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * 取得bucket的静态網站托管狀態
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function getBucketWebsite($ossClient, $bucket)
{
    $websiteConfig = null;
    try {
        $websiteConfig = $ossClient->getBucketWebsite($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($websiteConfig->serializeToXml() . "\n");
}

/**
 * 刪除bucket的静态網站托管模式配置
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function deleteBucketWebsite($ossClient, $bucket)
{
    try {
        $ossClient->deleteBucketWebsite($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
