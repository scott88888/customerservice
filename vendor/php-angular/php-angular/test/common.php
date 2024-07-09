<?php
header('Content-Type: text/html; charset=utf-8;');
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

// 開始時間
$start_time = microtime(true);

require '../src/Angular.php';

// 配置
$config = [
    'debug'            => true, // 是否開啟调试, 開啟调试会实时產生快取
    'tpl_path'         => './view/', // 模板根目錄
    'tpl_suffix'       => '.html', // 模板後缀
    'tpl_cache_path'   => './cache/', // 模板快取目錄
    'tpl_cache_suffix' => '.php', // 模板後缀
    'attr'             => 'php-', // 标签前缀
    'max_tag'          => 10000, // 标签的最大解析次數
];

// 自訂扩展, 列印变量的值
\PHPAngular\Angular::extend('dump', function ($content, $param, $angular) {
    $old = $param['html'];
    $new = '<pre>';
    unset($param[0], $param[1], $param[2], $param[3], $param[4], $param[5]);
    $new .= '<?php var_dump(' . $param['value'] . ');  ?>';
    // var_dump($angular->config);
    $new .= '<pre>';
    return str_replace($old, $new, $content);
});

// 自訂扩展, 变量+1
\PHPAngular\Angular::extend('inc', function ($content, $param, $angular) {
    $old = $param['html'];
    $new = '<?php ' . $param['value'] . '++; ?>';
    $new .= \PHPAngular\Angular::removeExp($old, $param['exp']);
    return str_replace($old, $new, $content);
});

// 自訂扩展, 变量-1
\PHPAngular\Angular::extend('dec', function ($content, $param, $angular) {
    $old = $param['html'];
    $new = '<?php ' . $param['value'] . '--; ?>';
    $new .= \PHPAngular\Angular::removeExp($old, $param['exp']);
    return str_replace($old, $new, $content);
});


function load($key)
{
    return include './data/' . $key . '.php';
}

// 實例化
$view = new \PHPAngular\Angular($config);

// 导航
$navs = load('navs');
$view->assign('navs', $navs);
$view->assign('start_time', $start_time);
