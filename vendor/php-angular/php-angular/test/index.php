<?php
require './common.php';

// 模拟使用者列表
$data = [
    'title' => '首頁',
    'list'  => [
        ['id' => 1, 'name' => 'user_1', 'email' => 'email_1@qq.com', 'status' => 1],
        ['id' => 2, 'name' => 'user_2', 'email' => 'email_2@qq.com', 'status' => 0],
        ['id' => 3, 'name' => 'user_3', 'email' => 'email_3@qq.com', 'status' => -1],
        ['id' => 4, 'name' => 'user_4', 'email' => 'email_4@qq.com', 'status' => 1],
        ['id' => 5, 'name' => 'user_5', 'email' => 'email_5@qq.com', 'status' => 1],
    ],
];

// 树状结构
$menus = [
    [
        'title' => '選單1',
        'sub'   => [
            ['title' => '選單1.1'],
            ['title' => '選單1.2'],
            ['title' => '選單1.3'],
            ['title' => '選單1.4'],
        ],
    ],
    [
        'title' => '選單2',
        'sub'   => [
            ['title' => '選單2.1'],
            ['title' => '選單2.2'],
            ['title' => '選單2.3'],
            ['title' => '選單2.4'],
        ],
    ],
    [
        'title' => '選單3',
        'sub'   => [
            [
                'title' => '選單3.1',
                'sub'   => [
                    ['title' => '選單3.1.1'],
                    ['title' => '選單3.1.2'],
                    [
                        'title' => '選單3.1.3',
                        'sub'   => [
                            ['title' => '選單3.1.3.1'],
                            ['title' => '選單3.1.3.2'],
                        ],
                    ],
                ],
            ],
            ['title' => '選單3.2'],
            ['title' => '選單3.3'],
            ['title' => '選單3.4'],
        ],
    ],
];

$view->assign('pagecount', 100);
$view->assign('p', isset($_GET['p']) ? $_GET['p'] : 1);
$view->assign('page', function ($p) {
    return 'index.php?p=' . $p;
});

// 向模板引擎設定資料
$view->assign($data);
$view->assign('start_time', $start_time);
$view->assign('menus', $menus);

// 输出解析结果
$view->display('index');

// 返回输出结果
// $html = $view->fetch('index');
// echo $html;

// 取得混编程式碼
// $php_code = $view->compiler('index');
