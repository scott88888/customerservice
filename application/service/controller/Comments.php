<?php


namespace app\service\controller;

use app\service\model\Comment;
use app\service\model\CommentSetting;
use think\Db;

/**
 *
 * 後台頁面控制器.
 */
class Comments extends Base
{

    public function index()
    {
        if ($this->request->isAjax()) return Comment::getList();
        $group = Db::name("wolive_group")->where(['business_id'=>$_SESSION['Msg']['business_id']])->select();
        $this->assign('group', $group);
        return $this->fetch();
    }

    public function setting(){
        if ($this->request->isAjax()){
            $setting = CommentSetting::get(['business_id'=>$_SESSION['Msg']['business_id']]);
            if (!empty($setting)) $setting['comments'] = json_decode($setting['comments'],true);
            $this->success('取得成功','',$setting);
        }
        return $this->fetch();
    }

    public function save(){
        $data = $this->request->post();
        $comments = $this->request->post('comments/a',[]);
        $data['comments'] = json_encode($comments);
        $data['business_id'] = $_SESSION['Msg']['business_id'];
        if (empty($data['title'])) $this->error("評價说明不能為空");
        if (empty($comments)) $this->error("評價條目不能為空");
        foreach ($comments as $v) {
            if (mb_strlen($v)>8 || empty($v)) $this->error("評價條目限8字且不能為空");
        }
        if ($data['word_switch'] == 'open') {
            if (mb_strlen($data['word_title']) >8 || !isset($data['word_title'])) $this->error("評價條目限8字且不能為空");
        }
        $setting = CommentSetting::get(['business_id'=>$data['business_id']]);
        if (!empty($setting)) $res = $setting->save($data);
        else $res = CommentSetting::create($data);
        if ($res !== false) $this->success('操作成功');
        $this->error("操作失敗");
    }
}