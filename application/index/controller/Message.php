<?php


namespace app\index\controller;

use think\Controller;
use app\index\model\User;

/**
 *
 * 留言控制器.
 * Class Message
 * @package app\index\controller
 */
class Message extends Controller
{
    /**
     * 留言首頁.
     *
     * @return mixed
     */
    private function index()
    {
        return $this->fetch();
    }

    /**
     * 保存留言信息.
     *
     * @return array|string|true
     */
    private function keep()
    {
        $post = $this->request->post();

        $content = $post['content'];
        $new_content = str_replace("<", "&lt;", $content);

        $post['content'] = $new_content;
        //驗證
        $result = $this->validate($post, 'Message');

        if ($result === true) {
            $res = User::table('wolive_message')->insert($post);
            if ($res) {
                return "送出成功";
            } else {
                return "送出失敗";
            }

        } else {

            return $result;
        }

    }
}
