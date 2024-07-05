<?php


namespace app\admin\controller;

use app\admin\model\Admins;
use app\admin\model\Chats;
use app\admin\model\CommentSetting;
use app\admin\model\Queue;
use app\admin\model\TplService;
use app\admin\model\Visiter;
use app\admin\model\WechatPlatform;
use app\common\lib\CurlUtils;
use app\common\lib\Lock;
use app\common\lib\Storage;
use app\common\lib\storage\StorageException;
use app\extra\push\Pusher;
use think\Db;
use think\Exception;
use think\Log;
use think\Controller;
use app\admin\iplocation\Ip;
/**
 *
 * 设置控制器.
 */
class Fanyi extends Controller
{
    public function isTrans($text,$to,$business_id){
        if($to == 'cn') return $text;
        if(strpos($text, '<img') !== false) return $text;
        if(strpos($text, '<a') !== false) return $text;
        if(strpos($text, '<video') !== false) return $text;
        if(strpos($text, '<p') !== false) return $text;
        $business = Db::table('wolive_business')->where(['id'=>$business_id])->field("bd_trans_appid,bd_trans_secret,auto_trans,trans_type,google_trans_key")->find();
        if($business['auto_trans']){
            if($business['bd_trans_appid']&&$business['bd_trans_secret']&&$business['trans_type']==0){
                $to = config('lang_trans')[$to];
                $salt = time();
                $sign = md5($business['bd_trans_appid'].$text.$salt.$business['bd_trans_secret']);
                $query = http_build_query([
                    "q" => $text,
                    "from" => 'auto',
                    "to" => $to,
                    "appid" => $business['bd_trans_appid'],
                    "salt" => $salt,
                    "sign" => $sign,
                ]);
                try{
                    $res = file_get_contents("http://api.fanyi.baidu.com/api/trans/vip/translate?$query");
                    $res = json_decode($res,true);
                    if(!isset($res['error_code'])&&isset($res['trans_result'][0]['dst'])){
                        return $res['trans_result'][0]['dst'];
                    }else{
                        return $text;
                    }
                }catch (Exception $e) {
                    return $text;
                }
            }
            if($business['trans_type']){
                $to = config('lang_trans_g')[$to];
                $stream_opts = [
                    "ssl" => [
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ]
                ];
                $query = http_build_query([
                    "q" => $text,
                    "target" => $to,
                    "format" => 'text',
                    "key"=> $business['google_trans_key'],
                ]);
                try{
                    $res = file_get_contents("https://translation.googleapis.com/language/translate/v2?$query",false, stream_context_create($stream_opts));
                    $res = json_decode($res,true);
                    if(isset($res['data']['translations'][0]['translatedText'])){
                        return $res['data']['translations'][0]['translatedText'];
                    }else{
                        return $text;
                    }
                }catch (Exception $e) {
                    return $text;
                }
            }
            return $text;
        }else{
            return $text;
        }
    }

}