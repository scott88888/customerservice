<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2020/1/30
 * Time: 15:26
 */

namespace app\service\model;

use think\Db;
use think\Exception;
use think\Hook;
use think\Model;

class Business extends Model
{
    protected $table = 'wolive_business';

    public function service()
    {
        return $this->hasOne('Service','business_id','id');
    }

    public function admin()
    {
        return $this->hasOne('Admin','id','admin_id');
    }

    public static function addBusiness($post)
    {
        //账号注册时需要开启事务,避免出现垃圾数据
        Db::startTrans();
        try
        {
            $business = Business::create([
                'admin_id' => 0,
                'business_name' => $post['business_name'],
                'max_count' => $post['max_count'],
                'is_delete' => 0,
                'expire_time' => $post['expire_time']
            ]);

            Service::create([
                'business_id' => $business->id,
                'level' => 'super_manager',
                'user_name' => $post['business_name'],
                'nick_name' => $post['business_name'],
                'password' => md5($post['business_name'] . "hjkj" . $post['password'])
            ]);
            Db::commit();
            return true;
        }
        catch (Exception $e)
        {
            Db::rollback();
            return false;
        }
    }

    public static function editBusiness($post)
    {
        Db::startTrans();
        try
        {
            $business = Business::where('id',$post['id'])->update([
                'business_name' => $post['business_name'],
                'max_count' => $post['max_count'],
                'expire_time' => $post['expire_time'],
                'copyright' => $post['copyright'],
                'remark' => $post['remark'],
                'logo' => $post['logo'],
            ]);
            
            Db::commit();

            //注册成功的事件
            Hook::listen("dianqilai_edit_successed");

            return TRUE;
        }
        catch (Exception $e)
        {
            Db::rollback();
            return false;
        }
    }

    public static function getSub()
    {

    }
}