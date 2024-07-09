<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;

$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();

//******************************* 简單使用 ****************************************************************

//建立bucket
$ossClient->createBucket($bucket, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
Common::println("bucket $bucket created");

// 判断Bucket是否存在
$doesExist = $ossClient->doesBucketExist($bucket);
Common::println("bucket $bucket exist? " . ($doesExist ? "yes" : "no"));

// 取得Bucket列表
$bucketListInfo = $ossClient->listBuckets();

// 設定bucket的ACL
$ossClient->putBucketAcl($bucket, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
Common::println("bucket $bucket acl put");
// 取得bucket的ACL
$acl = $ossClient->getBucketAcl($bucket);
Common::println("bucket $bucket acl get: " . $acl);


//******************************* 完整用法参考下面函數 ****************************************************

createBucket($ossClient, $bucket);
doesBucketExist($ossClient, $bucket);
deleteBucket($ossClient, $bucket);
putBucketAcl($ossClient, $bucket);
getBucketAcl($ossClient, $bucket);
listBuckets($ossClient);

/**
 * 建立一个存储空間
 * acl 指的是bucket的訪問控制权限，有三种，私有读寫，公共读私有寫，公共读寫。
 * 私有读寫就是只有bucket的拥有者或授权使用者才有权限操作
 * 三种权限分别對应 (OssClient::OSS_ACL_TYPE_PRIVATE，OssClient::OSS_ACL_TYPE_PUBLIC_READ, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE)
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 要建立的存储空間名稱
 * @return null
 */
function createBucket($ossClient, $bucket)
{
    try {
        $ossClient->createBucket($bucket, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 *  判断Bucket是否存在
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 存储空間名稱
 */
function doesBucketExist($ossClient, $bucket)
{
    try {
        $res = $ossClient->doesBucketExist($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    if ($res === true) {
        print(__FUNCTION__ . ": OK" . "\n");
    } else {
        print(__FUNCTION__ . ": FAILED" . "\n");
    }
}

/**
 * 刪除bucket，如果bucket不為空則bucket無法刪除成功， 不為空表示bucket既没有object，也没有未完成的multipart上傳时的parts
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 待刪除的存储空間名稱
 * @return null
 */
function deleteBucket($ossClient, $bucket)
{
    try {
        $ossClient->deleteBucket($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * 設定bucket的acl配置
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function putBucketAcl($ossClient, $bucket)
{
    $acl = OssClient::OSS_ACL_TYPE_PRIVATE;
    try {
        $ossClient->putBucketAcl($bucket, $acl);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * 取得bucket的acl配置
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function getBucketAcl($ossClient, $bucket)
{
    try {
        $res = $ossClient->getBucketAcl($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print('acl: ' . $res);
}


/**
 * 列出使用者所有的Bucket
 *
 * @param OssClient $ossClient OssClient實例
 * @return null
 */
function listBuckets($ossClient)
{
    $bucketList = null;
    try {
        $bucketListInfo = $ossClient->listBuckets();
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    $bucketList = $bucketListInfo->getBucketList();
    foreach ($bucketList as $bucket) {
        print($bucket->getLocation() . "\t" . $bucket->getName() . "\t" . $bucket->getCreatedate() . "\n");
    }
}
