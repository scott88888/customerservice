# ChangeLog - Aliyun OSS SDK for PHP

## v2.3.0 / 2018-01-05

* 修复：putObject支援建立空文件
* 修复：createBucket支援IA/Archive
* 增加：支援restoreObject
* 增加：支援Symlink功能
* 增加：支援getBucketLocation
* 增加：支援getBucketMeta
* 增加：支援代理服务器Proxy

## v2.2.4 / 2017-04-25

* fix getObject to local file bug

## v2.2.3 / 2017-04-14

* fix md5 check

## v2.2.2 / 2017-01-18

* 解决在php7上运行連結數和内存bug

## v2.2.1 / 2016-12-01

* 禁止http curl自動填充Accept-Encoding

## v2.2.0 / 2016-11-22

* 修复PutObject/CompleteMultipartUpload的返回值問題(#26)

## v2.1.0 / 2016-11-12

* 增加[RTMP](https://help.aliyun.com/document_detail/44297.html)接口
* 增加支援[圖片服务](https://help.aliyun.com/document_detail/44686.html)

## v2.0.7 / 2016-06-17

* Support append object

## v2.0.6

* Trim access key id/secret and endpoint
* Refine tests and setup travis CI

## v2.0.5

* 增加Add/Delete/Get BucketCname接口

## v2.0.4

* 增加Put/Get Object Acl接口

## v2.0.3

* 修复Util中的常量定義在低于5.6的PHP版本中报错的問題

## v2.0.2

* 修复multipart上傳时無法指定Content-Type的問題

## v2.0.1

* 增加對ListObjects/ListMultipartUploads时特殊字符的處理
* 提供接口取得OssException中的详细訊息


## 2015.11.25

* **大版本升级，不再兼容以前接口，新版本對易用性做了很大的改进，建议使用者迁移到新版本。**

## 修改内容

* 不再支援PHP 5.2版本

### 新增内容

* 引入命名空間
* 接口命名修正，采用駝峰式命名
* 接口入参修改，把常用参數从Options参數中提出来
* 接口返回结果修改，對返回结果进行處理，使用者可以直接得到容易處理的資料结构　
* OssClient的构造函數变更
* 支援CNAME和IP格式的Endpoint地址
* 重新整理sample文件组织结构，使用function组织功能點
* 增加設定連結超时，請求超时的接口
* 去掉Object Group相關的已经过时的接口
* OssException中的message改為英文

### 問題修复

* object名稱校验不完备
