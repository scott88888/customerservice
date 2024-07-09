<?php


namespace app\service\model;

use think\Model;
use app\service\iplocation\Ip;

/**
 * 資料模型類.
 */
class AdminLog extends Model
{
    // 取得日誌列表
    public static function getLog()
    {
        $where = [];
        $limit = input('get.limit');
        $where['uid'] =  $_SESSION['Msg']['service_id'];
        $list = self::table('wolive_admin_log')->order('id','desc')->where($where)->paginate($limit)->each(function($item,$key){
            $ip_area = Ip::find($item['ip']);
            $item['ip'] = $item['ip']."【{$ip_area[0]}{$ip_area[1]}{$ip_area[2]}】";
            $service = self::table('wolive_service')->where(['service_id'=>$item['uid']])->find();
            $item['user_name'] = $service['user_name']?:'未知使用者';
            return $item;
        });
        return ['code'=>0,'data'=>$list->items(),'count' => $list->total(), 'limit' => $limit];
    }
}
