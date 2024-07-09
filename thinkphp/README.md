ThinkPHP 5.0
===============

[![StyleCI](https://styleci.io/repos/48530411/shield?style=flat&branch=master)](https://styleci.io/repos/48530411)
[![Build Status](https://travis-ci.org/top-think/framework.svg?branch=master)](https://travis-ci.org/top-think/framework)
[![codecov.io](http://codecov.io/github/top-think/framework/coverage.svg?branch=master)](http://codecov.io/github/github/top-think/framework?branch=master)
[![Total Downloads](https://poser.pugx.org/topthink/framework/downloads)](https://packagist.org/packages/topthink/framework)
[![Latest Stable Version](https://poser.pugx.org/topthink/framework/v/stable)](https://packagist.org/packages/topthink/framework)
[![Latest Unstable Version](https://poser.pugx.org/topthink/framework/v/unstable)](https://packagist.org/packages/topthink/framework)
[![License](https://poser.pugx.org/topthink/framework/license)](https://packagist.org/packages/topthink/framework)

ThinkPHP5在保持快速開发和大道至简的核心理念不变的同时，PHP版本要求提升到5.4，优化核心，减少依赖，基于全新的架构思想和命名空間實現，是ThinkPHP突破原有框架思路的颠覆之作，其主要特性包括：

 + 基于命名空間和众多PHP新特性
 + 核心功能组件化
 + 强化路由功能
 + 更灵活的控制器
 + 重构的模型和資料庫類
 + 配置文件可分离
 + 重寫的自動驗證和完成
 + 简化扩展機制
 + API支援完善
 + 改进的Log類
 + 命令行訪問支援
 + REST支援
 + 引导文件支援
 + 方便的自動產生定義
 + 真正惰性載入
 + 分布式环境支援
 + 支援Composer
 + 支援MongoDb

> ThinkPHP5的运行环境要求PHP5.4以上。

详细開发文档参考 [ThinkPHP5完全開发手册](http://www.kancloud.cn/manual/thinkphp5) 以及[ThinkPHP5入门系列教程](http://www.kancloud.cn/special/thinkphp5_quickstart)

## 目錄结构

初始的目錄结构如下：

~~~
www  WEB部署目錄（或者子目錄）
├─application           應用目錄
│  ├─common             公共模組目錄（可以更改）
│  ├─module_name        模組目錄
│  │  ├─config.php      模組配置文件
│  │  ├─common.php      模組函數文件
│  │  ├─controller      控制器目錄
│  │  ├─model           模型目錄
│  │  ├─view            视圖目錄
│  │  └─ ...            更多類庫目錄
│  │
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函數文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           應用行為扩展定義文件
│  └─database.php       資料庫配置文件
│
├─public                WEB目錄（對外訪問目錄）
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重寫
│
├─thinkphp              框架系统目錄
│  ├─lang               語言文件目錄
│  ├─library            框架類庫目錄
│  │  ├─think           Think類庫包目錄
│  │  └─traits          系统Trait目錄
│  │
│  ├─tpl                系统模板目錄
│  ├─base.php           基础定義文件
│  ├─console.php        控制台入口文件
│  ├─convention.php     框架惯例配置文件
│  ├─helper.php         助手函數文件
│  ├─phpunit.xml        phpunit配置文件
│  └─start.php          框架入口文件
│
├─extend                扩展類庫目錄
├─runtime               應用的运行时目錄（可寫，可定制）
├─vendor                第三方類庫目錄（Composer依赖庫）
├─build.php             自動產生定義文件（参考）
├─composer.json         composer 定義文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
~~~

> router.php用于php自带webserver支援，可用于快速测试
> 切换到public目錄後，启動命令：php -S localhost:8888  router.php
> 上面的目錄结构和名稱是可以改变的，这取决于你的入口文件和配置参數。

## 命名规范

ThinkPHP5的命名规范遵循`PSR-2`规范以及`PSR-4`自動載入规范。

## 参与開发
註冊並登入 Github 帐号， fork 本项目並进行改動。

更多细节参阅 [CONTRIBUTING.md](CONTRIBUTING.md)

## 版权訊息

ThinkPHP遵循Apache2開源协议发布，並提供免费使用。

本项目包含的第三方源碼和二进制文件之版权訊息另行标注。

版权所有Copyright © 2006-2018 by ThinkPHP (http://thinkphp.cn)

All rights reserved。

ThinkPHP® 商标和著作权所有者為上海顶想訊息科技有限公司。

更多细节参阅 [LICENSE.txt](LICENSE.txt)
