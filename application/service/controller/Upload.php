<?php


namespace app\service\controller;

/**
 *
 * 后台頁面控制器.
 */
class Upload extends Base
{
    public function ueditor(){
        !defined('UEDITORPATH') && define('UEDITORPATH', 'UEDITORPATH/');
        require './assets/ueditor/php/controller.php';
    }
}
