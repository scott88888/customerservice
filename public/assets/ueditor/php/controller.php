<?php
defined('UEDITORPATH') OR exit('No direct script access allowed');

header('Access-Control-Allow-Origin:*'); //临时處理，後面在强化它
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');

//chdir(__DIR__);

if (is_file("./assets/ueditor/php/config.php")) {
	$CONFIG = require "./assets/ueditor/php/config.php";
} elseif (is_file("./assets/ueditor/php/config.php")) {
	$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(APP_PATH."./assets/ueditor/php/config.json")), true);
} else {
	echo json_encode(array(
		'state'=> '無权限訪問./assets/ueditor/php/config.php文件'
	), JSON_UNESCAPED_UNICODE);exit;
}

if (isset($CONFIG['imageAltValue']) && $CONFIG['imageAltValue'] == 'name') {
    $CONFIG["imgTitleTag"] = '';
} else {
    $CONFIG["imgTitleTag"] = UEDITOR_IMG_TITLE;
}
if(!isset($_GET['action'])){
    echo json_encode(array(
        'state'=> '参數非法'
    ), JSON_UNESCAPED_UNICODE);exit;
}

$action = $_GET['action'];


    // 驗證了才能上傳
    switch ($action) {
        case 'config':
            $result =  json_encode($CONFIG, JSON_UNESCAPED_UNICODE);
            break;

        /* 上傳圖片 */
        case 'uploadimage':
            /* 上傳涂鸦 */
        case 'uploadscrawl':
            /* 上傳视频 */
        case 'uploadvideo':
            /* 上傳文件 */
        case 'uploadfile':
            $result = include("action_upload.php");
            break;

        /* 列出圖片 */
        case 'listimage':
            $result = include("action_list.php");
            break;
        /* 列出文件 */
        case 'listfile':
            $result = include("action_list.php");
            break;
        /* 列出文件 */
        case 'listvideo':
            $result = include("action_list.php");
            break;

        /* 抓取远程文件 */
        case 'catchimage':
            //$result = include("action_crawler.php");
            break;

        default:
            $result = json_encode(array(
                'state'=> '請求地址出错'
            ), JSON_UNESCAPED_UNICODE);
            break;
    }


/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参數不合法'
        ), JSON_UNESCAPED_UNICODE);
    }
} else {
    echo $result;
}
exit;