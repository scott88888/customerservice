<?php
defined('UEDITORPATH') OR exit('No direct script access allowed');

/* 前后端通信相關的配置,注释只允许使用多行方式 */

return [

    /* 上传图片配置项 */
    "imageAltValue" => "name", /*图片alt属性和title属性填充值：title為内容标题字段值、name為图片名稱*/
    "imageActionName" => "uploadimage", /* 执行上传图片的action名稱 */
    "imageFieldName" => "upfile", /* 送出的图片表單名稱 */
    "imageMaxSize" => 2048000, /* 上传大小限制，單位B */
    "imageAllowFiles" => [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 上传图片格式显示 */
    "imageCompressEnable" => true, /* 是否压缩图片,默认是true */
    "imageCompressBorder" => 1600, /* 图片压缩最長边限制 */
    "imageInsertAlign" => "none", /* 插入的图片浮動方式 */
    "imageUrlPrefix" => "", /* 图片訪問路径前缀 */
    "imagePathFormat" => "/ueditor/image/{yyyy}{mm}/{time}{rand:6}", /* 上传保存路径,可以自訂保存路径和文件名格式 */
                                /* {filename} 会替换成原文件名,配置这项需要注意中文乱碼問題 */
                                /* {rand:6} 会替换成随机數,後面的數字是随机數的位數 */
                                /* {time} 会替换成時間戳 */
                                /* {yyyy} 会替换成四位年份 */
                                /* {yy} 会替换成两位年份 */
                                /* {mm} 会替换成两位月份 */
                                /* {dd} 会替换成两位日期 */
                                /* {hh} 会替换成两位小时 */
                                /* {ii} 会替换成两位分钟 */
                                /* {ss} 会替换成两位秒 */
                                /* 非法字符 \ : * ? " < > | */
                                /* 具請体看线上文档: fex.baidu.com/ueditor/#use-format_upload_filename */

    /* 涂鸦图片上传配置项 */
    "scrawlActionName" => "uploadscrawl", /* 执行上传涂鸦的action名稱 */
    "scrawlFieldName" => "upfile", /* 送出的图片表單名稱 */
    "scrawlPathFormat" => "/ueditor/image/{yyyy}{mm}/{time}{rand:6}", /* 上传保存路径,可以自訂保存路径和文件名格式 */
    "scrawlMaxSize" => 2048000, /* 上传大小限制，單位B */
    "scrawlUrlPrefix" => "", /* 图片訪問路径前缀 */
    "scrawlInsertAlign" => "none",

    /* 截图工具上传 */
    "snapscreenActionName" => "uploadimage", /* 执行上传截图的action名稱 */
    "snapscreenPathFormat" => "/ueditor/image/{yyyy}{mm}/{time}{rand:6}", /* 上传保存路径,可以自訂保存路径和文件名格式 */
    "snapscreenUrlPrefix" => "", /* 图片訪問路径前缀 */
    "snapscreenInsertAlign" => "none", /* 插入的图片浮動方式 */

    /* 上传视频配置 */
    "videoActionName" => "uploadvideo", /* 执行上传视频的action名稱 */
    "videoFieldName" => "upfile", /* 送出的视频表單名稱 */
    "videoPathFormat" => "/ueditor/video/{yyyy}{mm}/{time}{rand:6}", /* 上传保存路径,可以自訂保存路径和文件名格式 */
    "videoUrlPrefix" => "", /* 视频訪問路径前缀 */
    "videoMaxSize" => 102400000, /* 上传大小限制，單位B，默认100MB */
    "videoAllowFiles" => [
        ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
        ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid"], /* 上传视频格式显示 */

    /* 上传文件配置 */
    "fileActionName" => "uploadfile", /* controller里,执行上传视频的action名稱 */
    "fileFieldName" => "upfile", /* 送出的文件表單名稱 */
    "filePathFormat" => "/ueditor/file/{yyyy}{mm}/{time}{rand:6}", /* 上传保存路径,可以自訂保存路径和文件名格式 */
    "fileUrlPrefix" => "", /* 文件訪問路径前缀 */
    "fileMaxSize" => 51200000, /* 上传大小限制，單位B，默认50MB */
    "fileAllowFiles" => [
        ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
        ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
        ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
        ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
    ], /* 上传文件格式显示 */

    /* 列出目录下的图片 */
    "imageManagerActionName" => "listimage", /* 执行图片管理的action名稱 */
    "imageManagerListSize" => 20, /* 每次列出文件數量 */
    "imageManagerUrlPrefix" => "", /* 图片訪問路径前缀 */
    "imageManagerInsertAlign" => "none", /* 插入的图片浮動方式 */
    "imageManagerAllowFiles" => [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 列出的文件类型 */

    /* 列出目录下的文件 */
    "fileManagerActionName" => "listfile", /* 执行文件管理的action名稱 */
    "fileManagerUrlPrefix" => "", /* 文件訪問路径前缀 */
    "fileManagerListSize" => 20, /* 每次列出文件數量 */
    "fileManagerAllowFiles" => [
        ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
        ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
        ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
        ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
    ] /* 列出的文件类型 */

];