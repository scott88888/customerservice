# Aliyun OSS SDK for PHP

[![Latest Stable Version](https://poser.pugx.org/aliyuncs/oss-sdk-php/v/stable)](https://packagist.org/packages/aliyuncs/oss-sdk-php)
[![Build Status](https://travis-ci.org/aliyun/aliyun-oss-php-sdk.svg?branch=master)](https://travis-ci.org/aliyun/aliyun-oss-php-sdk)
[![Coverage Status](https://coveralls.io/repos/github/aliyun/aliyun-oss-php-sdk/badge.svg?branch=master)](https://coveralls.io/github/aliyun/aliyun-oss-php-sdk?branch=master)

## [README of English](https://github.com/aliyun/aliyun-oss-php-sdk/blob/master/README.md)

## 概述

阿里云對象存储（Object Storage Service，简称OSS），是阿里云對外提供的海量、安全、低成本、高可靠的云存储服务。使用者可以通過调用API，在任何應用、任何時間、任何地點上傳和下载資料，也可以通過使用者Web控制台對資料进行简單的管理。OSS适合存放任意文件類型，适合各种網站、開发企业及開发者使用。


## 运行环境
- PHP 5.3+
- cURL extension

提示：

- Ubuntu下可以使用apt-get包管理器安装php的cURL扩展 `sudo apt-get install php5-curl`

## 安装方法

1. 如果您通過composer管理您的项目依赖，可以在你的项目根目錄运行：

        $ composer require aliyuncs/oss-sdk-php

   或者在你的`composer.json`中声明對Aliyun OSS SDK for PHP的依赖：

        "require": {
            "aliyuncs/oss-sdk-php": "~2.0"
        }

   然後通過`composer install`安装依赖。composer安装完成後，在您的PHP程式碼中引入依赖即可：

        require_once __DIR__ . '/vendor/autoload.php';

2. 您也可以直接下载已经打包好的[phar文件][releases-page]，然後在你
   的程式碼中引入这个文件即可：

        require_once '/path/to/oss-sdk-php.phar';

3. 下载SDK源碼，在您的程式碼中引入SDK目錄下的`autoload.php`文件：

        require_once '/path/to/oss-sdk/autoload.php';

## 快速使用

### 常用類

| 類名 | 解釋 |
|:------------------|:------------------------------------|
|OSS\OssClient | OSS客户端類，使用者通過OssClient的實例调用接口 |
|OSS\Core\OssException | OSS异常類，使用者在使用的过程中，只需要注意这个异常|

### OssClient初始化

SDK的OSS操作通過OssClient類完成的，下面程式碼建立一个OssClient對象:

```php
<?php
$accessKeyId = "<您从OSS获得的AccessKeyId>"; ;
$accessKeySecret = "<您从OSS获得的AccessKeySecret>";
$endpoint = "<您选定的OSS資料中心訪問域名，例如oss-cn-hangzhou.aliyuncs.com>";
try {
    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
} catch (OssException $e) {
    print $e->getMessage();
}
```

### 文件操作

文件(又称對象,Object)是OSS中最基本的資料單元，您可以把它简單地理解為文件，用下面程式碼可以實現一个Object的上傳：

```php
<?php
$bucket = "<您使用的Bucket名字，注意命名规范>";
$object = "<您使用的Object名字，注意命名规范>";
$content = "Hello, OSS!"; // 上傳的文件内容
try {
    $ossClient->putObject($bucket, $object, $content);
} catch (OssException $e) {
    print $e->getMessage();
}
```

### 存储空間操作

存储空間(又称Bucket)是一个使用者用来管理所存储Object的存储空間,對于使用者来说是一个管理Object的單元，所有的Object都必须隶属于某个Bucket。您可以按照下面的程式碼新建一个Bucket：

```php
<?php
$bucket = "<您使用的Bucket名字，注意命名规范>";
try {
    $ossClient->createBucket($bucket);
} catch (OssException $e) {
    print $e->getMessage();
}
```

### 返回结果處理

OssClient提供的接口返回返回資料分為两种：

* Put，Delete類接口，接口返回null，如果没有OssException，即可认為操作成功
* Get，List類接口，接口返回對应的資料，如果没有OssException，即可认為操作成功，举个例子：

```php
<?php
$bucketListInfo = $ossClient->listBuckets();
$bucketList = $bucketListInfo->getBucketList();
foreach($bucketList as $bucket) {
    print($bucket->getLocation() . "\t" . $bucket->getName() . "\t" . $bucket->getCreatedate() . "\n");
}
```
上面程式碼中的$bucketListInfo的資料類型是 `OSS\Model\BucketListInfo`


### 运行Sample程序

1. 修改 `samples/Config.php`， 补充配置訊息
2. 執行 `cd samples/ && php RunAll.php`

### 运行單元测试

1. 執行`composer install`下载依赖的庫
2. 設定环境变量

        export OSS_ACCESS_KEY_ID=access-key-id
        export OSS_ACCESS_KEY_SECRET=access-key-secret
        export OSS_ENDPOINT=endpoint
        export OSS_BUCKET=bucket-name

3. 執行 `php vendor/bin/phpunit`

## License

- MIT

## 联系我们

- [阿里云OSS官方網站](http://oss.aliyun.com)
- [阿里云OSS官方论坛](http://bbs.aliyun.com)
- [阿里云OSS官方文档中心](http://www.aliyun.com/product/oss#Docs)
- 阿里云官方技術支援：[送出工單](https://workorder.console.aliyun.com/#/ticket/createIndex)

[releases-page]: https://github.com/aliyun/aliyun-oss-php-sdk/releases
[phar-composer]: https://github.com/clue/phar-composer
