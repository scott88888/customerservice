<?php
defined('UEDITORPATH') OR exit('No direct script access allowed');

/* 前後端通信相關的配置,注释只允许使用多行方式 */

return [

    /* 上傳圖片配置项 */
    "imageAltValue" => "name", /*圖片alt属性和title属性填充值：title為内容標題字段值、name為圖片名稱*/
    "imageActionName" => "uploadimage", /* 執行上傳圖片的action名稱 */
    "imageFieldName" => "upfile", /* 送出的圖片表單名稱 */
    "imageMaxSize" => 2048000, /* 上傳大小限制，單位B */
    "imageAllowFiles" => [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 上傳圖片格式显示 */
    "imageCompressEnable" => true, /* 是否压缩圖片,默認是true */
    "imageCompressBorder" => 1600, /* 圖片压缩最長边限制 */
    "imageInsertAlign" => "none", /* 插入的圖片浮動方式 */
    "imageUrlPrefix" => "", /* 圖片訪問路徑前缀 */
    "imagePathFormat" => "/ueditor/image/{yyyy}{mm}/{time}{rand:6}", /* 上傳保存路徑,可以自訂保存路徑和文件名格式 */
                                /* {filename} 会替换成原文件名,配置这项需要注意中文乱碼問題 */
                                /* {rand:6} 会替换成随機數,後面的數字是随機數的位數 */
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

    /* 涂鸦圖片上傳配置项 */
    "scrawlActionName" => "uploadscrawl", /* 執行上傳涂鸦的action名稱 */
    "scrawlFieldName" => "upfile", /* 送出的圖片表單名稱 */
    "scrawlPathFormat" => "/ueditor/image/{yyyy}{mm}/{time}{rand:6}", /* 上傳保存路徑,可以自訂保存路徑和文件名格式 */
    "scrawlMaxSize" => 2048000, /* 上傳大小限制，單位B */
    "scrawlUrlPrefix" => "", /* 圖片訪問路徑前缀 */
    "scrawlInsertAlign" => "none",

    /* 截圖工具上傳 */
    "snapscreenActionName" => "uploadimage", /* 執行上傳截圖的action名稱 */
    "snapscreenPathFormat" => "/ueditor/image/{yyyy}{mm}/{time}{rand:6}", /* 上傳保存路徑,可以自訂保存路徑和文件名格式 */
    "snapscreenUrlPrefix" => "", /* 圖片訪問路徑前缀 */
    "snapscreenInsertAlign" => "none", /* 插入的圖片浮動方式 */

    /* 上傳视频配置 */
    "videoActionName" => "uploadvideo", /* 執行上傳视频的action名稱 */
    "videoFieldName" => "upfile", /* 送出的视频表單名稱 */
    "videoPathFormat" => "/ueditor/video/{yyyy}{mm}/{time}{rand:6}", /* 上傳保存路徑,可以自訂保存路徑和文件名格式 */
    "videoUrlPrefix" => "", /* 视频訪問路徑前缀 */
    "videoMaxSize" => 102400000, /* 上傳大小限制，單位B，默認100MB */
    "videoAllowFiles" => [
        ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
        ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid"], /* 上傳视频格式显示 */

    /* 上傳文件配置 */
    "fileActionName" => "uploadfile", /* controller里,執行上傳视频的action名稱 */
    "fileFieldName" => "upfile", /* 送出的文件表單名稱 */
    "filePathFormat" => "/ueditor/file/{yyyy}{mm}/{time}{rand:6}", /* 上傳保存路徑,可以自訂保存路徑和文件名格式 */
    "fileUrlPrefix" => "", /* 文件訪問路徑前缀 */
    "fileMaxSize" => 51200000, /* 上傳大小限制，單位B，默認50MB */
    "fileAllowFiles" => [
        ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
        ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
        ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
        ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
    ], /* 上傳文件格式显示 */

    /* 列出目錄下的圖片 */
    "imageManagerActionName" => "listimage", /* 執行圖片管理的action名稱 */
    "imageManagerListSize" => 20, /* 每次列出文件數量 */
    "imageManagerUrlPrefix" => "", /* 圖片訪問路徑前缀 */
    "imageManagerInsertAlign" => "none", /* 插入的圖片浮動方式 */
    "imageManagerAllowFiles" => [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 列出的文件類型 */

    /* 列出目錄下的文件 */
    "fileManagerActionName" => "listfile", /* 執行文件管理的action名稱 */
    "fileManagerUrlPrefix" => "", /* 文件訪問路徑前缀 */
    "fileManagerListSize" => 20, /* 每次列出文件數量 */
    "fileManagerAllowFiles" => [
        ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
        ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
        ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
        ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
    ] /* 列出的文件類型 */

];