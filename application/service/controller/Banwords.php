<?php


namespace app\service\controller;

use app\service\model\Banword;
use think\Db;

/**
 *
 * 后台頁面控制器.
 */
class Banwords extends Base
{

    public function index()
    {
        if ($this->request->isAjax()) return Banword::getList();
        return $this->fetch();
    }

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $status = $this->request->post('status/d',0);
            if (!is_int($status)) $this->error('匹配方式字段非法！');
            $res = Banword::where("id", $post['id'])->field(true)->update($post);
            if ($res) $this->success('修改成功');
            $this->error('修改失敗！');
        }
        $id = $this->request->get('id');
        $banword = Banword::get(['id'=>$id]);
        $this->assign('banword', $banword);
        return $this->fetch();
    }

    public function add()
    {
        if ($this->request->isAjax()) {
            $post=$this->request->post();
            $post['business_id']=$_SESSION['Msg']['business_id'];
            $status = $this->request->post('status/d',0);
            if (!is_int($status)) $this->error('匹配方式字段非法！');
            $res =Banword::insert($post);
            if ($res) $this->success('新增成功');
            $this->error('新增失敗！');
        }
        return $this->fetch();
    }

    public function remove()
    {
        $id = $this->request->get('id');
        if (Banword::destroy(['id' => $id])) $this->success('操作成功！');
        $this->error('操作失敗！');
    }
}