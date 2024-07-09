<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssUtil;
use OSS\Core\OssException;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//*******************************简單使用***************************************************************

/**
 * 查看完整用法中的 "putObjectByRawApis"函數，查看使用基础的分片上傳api进行文件上傳，使用者可以基于这个自行實現断點续傳等功能
 */

// 使用分片上傳接口上傳文件, 接口会根據文件大小决定是使用普通上傳还是分片上傳
$ossClient->multiuploadFile($bucket, "file.php", __FILE__, array());
Common::println("local file " . __FILE__ . " is uploaded to the bucket $bucket, file.php");


// 上傳本地目錄到bucket内的targetdir子目錄中
$ossClient->uploadDir($bucket, "targetdir", __DIR__);
Common::println("local dir " . __DIR__ . " is uploaded to the bucket $bucket, targetdir/");


// 列出當前未完成的分片上傳
$listMultipartUploadInfo = $ossClient->listMultipartUploads($bucket, array());


//******************************* 完整用法参考下面函數 ****************************************************

multiuploadFile($ossClient, $bucket);
putObjectByRawApis($ossClient, $bucket);
uploadDir($ossClient, $bucket);
listMultipartUploads($ossClient, $bucket);

/**
 * 通過multipart上傳文件
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 存储空間名稱
 * @return null
 */
function multiuploadFile($ossClient, $bucket)
{
    $object = "test/multipart-test.txt";
    $file = __FILE__;
    $options = array();

    try {
        $ossClient->multiuploadFile($bucket, $object, $file, $options);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ":  OK" . "\n");
}

/**
 * 使用基本的api分阶段进行分片上傳
 *
 * @param OssClient $ossClient OssClient實例
 * @param string $bucket 存储空間名稱
 * @throws OssException
 */
function putObjectByRawApis($ossClient, $bucket)
{
    $object = "test/multipart-test.txt";
    /**
     *  step 1. 初始化一个分块上傳事件, 也就是初始化上傳Multipart, 取得upload id
     */
    try {
        $uploadId = $ossClient->initiateMultipartUpload($bucket, $object);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": initiateMultipartUpload FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": initiateMultipartUpload OK" . "\n");
    /*
     * step 2. 上傳分片
     */
    $partSize = 10 * 1024 * 1024;
    $uploadFile = __FILE__;
    $uploadFileSize = filesize($uploadFile);
    $pieces = $ossClient->generateMultiuploadParts($uploadFileSize, $partSize);
    $responseUploadPart = array();
    $uploadPosition = 0;
    $isCheckMd5 = true;
    foreach ($pieces as $i => $piece) {
        $fromPos = $uploadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
        $toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
        $upOptions = array(
            $ossClient::OSS_FILE_UPLOAD => $uploadFile,
            $ossClient::OSS_PART_NUM => ($i + 1),
            $ossClient::OSS_SEEK_TO => $fromPos,
            $ossClient::OSS_LENGTH => $toPos - $fromPos + 1,
            $ossClient::OSS_CHECK_MD5 => $isCheckMd5,
        );
        if ($isCheckMd5) {
            $contentMd5 = OssUtil::getMd5SumForFile($uploadFile, $fromPos, $toPos);
            $upOptions[$ossClient::OSS_CONTENT_MD5] = $contentMd5;
        }
        //2. 将每一分片上傳到OSS
        try {
            $responseUploadPart[] = $ossClient->uploadPart($bucket, $object, $uploadId, $upOptions);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} OK\n");
    }
    $uploadParts = array();
    foreach ($responseUploadPart as $i => $eTag) {
        $uploadParts[] = array(
            'PartNumber' => ($i + 1),
            'ETag' => $eTag,
        );
    }
    /**
     * step 3. 完成上傳
     */
    try {
        $ossClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": completeMultipartUpload FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
}

/**
 * 按照目錄上傳文件
 *
 * @param OssClient $ossClient OssClient
 * @param string $bucket 存储空間名稱
 *
 */
function uploadDir($ossClient, $bucket)
{
    $localDirectory = ".";
    $prefix = "samples/codes";
    try {
        $ossClient->uploadDir($bucket, $prefix, $localDirectory);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
}

/**
 * 取得當前未完成的分片上傳列表
 *
 * @param $ossClient OssClient
 * @param $bucket   string
 */
function listMultipartUploads($ossClient, $bucket)
{
    $options = array(
        'max-uploads' => 100,
        'key-marker' => '',
        'prefix' => '',
        'upload-id-marker' => ''
    );
    try {
        $listMultipartUploadInfo = $ossClient->listMultipartUploads($bucket, $options);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": listMultipartUploads FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ . ": listMultipartUploads OK\n");
    $listUploadInfo = $listMultipartUploadInfo->getUploads();
    var_dump($listUploadInfo);
}
