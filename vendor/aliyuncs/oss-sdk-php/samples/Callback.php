<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//*******************************简單使用***************************************************************

/** putObject 使用callback上傳内容到oss文件
  * callbackurl参數指定請求回调的服务器url
  * callbackbodytype参數可為application/json或application/x-www-form-urlencoded, 可选参數，默認為application/x-www-form-urlencoded
  * OSS_CALLBACK_VAR参數可以不設定
  */
$url = 
    '{
        "callbackUrl":"callback.oss-demo.com:23450",
        "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
        "callbackBody":"bucket=${bucket}&object=${object}&etag=${etag}&size=${size}&mimeType=${mimeType}&imageInfo.height=${imageInfo.height}&imageInfo.width=${imageInfo.width}&imageInfo.format=${imageInfo.format}&my_var1=${x:var1}&my_var2=${x:var2}",
         "callbackBodyType":"application/x-www-form-urlencoded"

    }';
$var = 
    '{
        "x:var1":"value1",
        "x:var2":"值2"
    }';
$options = array(OssClient::OSS_CALLBACK => $url,
                 OssClient::OSS_CALLBACK_VAR => $var
                );
$result = $ossClient->putObject($bucket, "b.file", "random content", $options);
Common::println($result['body']);
Common::println($result['info']['http_code']);

/**
  * completeMultipartUpload 使用callback上傳内容到oss文件
  * callbackurl参數指定請求回调的服务器url
  * callbackbodytype参數可為application/json或application/x-www-form-urlencoded, 可选参數，默認為application/x-www-form-urlencoded
  * OSS_CALLBACK_VAR参數可以不設定
  */  
$object = "multipart-callback-test.txt";
$copiedObject = "multipart-callback-test.txt.copied";
$ossClient->putObject($bucket, $copiedObject, file_get_contents(__FILE__));

/**
  *  step 1. 初始化一个分块上傳事件, 也就是初始化上傳Multipart, 取得upload id
  */
$upload_id = $ossClient->initiateMultipartUpload($bucket, $object);

/**
 * step 2. uploadPartCopy
 */
$copyId = 1;
$eTag = $ossClient->uploadPartCopy($bucket, $copiedObject, $bucket, $object, $copyId, $upload_id);
$upload_parts[] = array(
    'PartNumber' => $copyId,
    'ETag' => $eTag,
    );
$listPartsInfo = $ossClient->listParts($bucket, $object, $upload_id);

/**
 * step 3.
 */
$json = 
    '{
        "callbackUrl":"callback.oss-demo.com:23450",
        "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
        "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size},\"x:var1\":${x:var1},\"x:var2\":${x:var2}}",
        "callbackBodyType":"application/json"
    }';
$var = 
    '{
        "x:var1":"value1",
        "x:var2":"值2"
    }';
$options = array(OssClient::OSS_CALLBACK => $json,
                 OssClient::OSS_CALLBACK_VAR => $var);

$result = $ossClient->completeMultipartUpload($bucket, $object, $upload_id, $upload_parts, $options);
Common::println($result['body']);
Common::println($result['info']['http_code']);
