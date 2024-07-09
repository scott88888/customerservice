# Qiniu Cloud SDK for PHP
[![doxygen.io](http://doxygen.io/github.com/qiniu/php-sdk/?status.svg)](http://doxygen.io/github.com/qiniu/php-sdk/)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://travis-ci.org/qiniu/php-sdk.svg)](https://travis-ci.org/qiniu/php-sdk)
[![Latest Stable Version](https://img.shields.io/packagist/v/qiniu/php-sdk.svg)](https://packagist.org/packages/qiniu/php-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/qiniu/php-sdk.svg)](https://packagist.org/packages/qiniu/php-sdk)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/qiniu/php-sdk/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/qiniu/php-sdk/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/qiniu/php-sdk/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/qiniu/php-sdk/?branch=master)
[![Join Chat](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/qiniu/php-sdk?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![@qiniu on weibo](http://img.shields.io/badge/weibo-%40qiniutek-blue.svg)](http://weibo.com/qiniutek)

## 安装

* 通過composer，这是推荐的方式，可以使用composer.json 声明依赖，或者运行下面的命令。SDK 包已经放到这里 [`qiniu/php-sdk`][install-packagist] 。
```bash
$ composer require qiniu/php-sdk
```
* 直接下载安装，SDK 没有依赖其他第三方庫，但需要参照 composer的autoloader，增加一个自己的autoloader程序。

## 运行环境

| Qiniu SDK版本 | PHP 版本 |
|:--------------------:|:---------------------------:|
|          7.x         |  cURL extension,   5.3 - 5.6,7.0 |
|          6.x         |  cURL extension,   5.2 - 5.6 |

## 使用方法

### 上傳
```php
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
...
    $upManager = new UploadManager();
    $auth = new Auth($accessKey, $secretKey);
    $token = $auth->uploadToken($bucketName);
    list($ret, $error) = $upManager->put($token, 'formput', 'hello world');
...
```

## 测试

``` bash
$ ./vendor/bin/phpunit tests/Qiniu/Tests/
```

## 常見問題

- $error保留了請求响应的訊息，失敗情况下ret 為none, 将$error可以列印出来，送出给我们。
- API 的使用 demo 可以参考 [單元测试](https://github.com/qiniu/php-sdk/blob/master/tests)。

## 程式碼贡献

详情参考[程式碼送出指南](https://github.com/qiniu/php-sdk/blob/master/CONTRIBUTING.md)。

## 贡献记录

- [所有贡献者](https://github.com/qiniu/php-sdk/contributors)

## 联系我们

- 如果需要帮助，請送出工單（在portal右側點擊咨询和建议送出工單，或者直接向 support@qiniu.com 發送邮件）
- 如果有什么問題，可以到问答社区提问，[问答社区](http://qiniu.segmentfault.com/)
- 更详细的文档，见[官方文档站](http://developer.qiniu.com/)
- 如果发现了bug， 欢迎送出 [issue](https://github.com/qiniu/php-sdk/issues)
- 如果有功能需求，欢迎送出 [issue](https://github.com/qiniu/php-sdk/issues)
- 如果要送出程式碼，欢迎送出 pull request
- 欢迎关注我们的[微信](http://www.qiniu.com/#weixin) [微博](http://weibo.com/qiniutek)，及时取得動态訊息。

## 程式碼许可

The MIT License (MIT).详情见 [License文件](https://github.com/qiniu/php-sdk/blob/master/LICENSE).

[packagist]: http://packagist.org
[install-packagist]: https://packagist.org/packages/qiniu/php-sdk
