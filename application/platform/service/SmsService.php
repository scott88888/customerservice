<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2020/4/17
 * Time: 17:18
 */
namespace app\platform\service;

use app\platform\enum\apps;
use app\platform\model\Option;
use Overtrue\EasySms\EasySms;
use think\Exception;

class SmsService
{
    /**
     * 發送短信
     */
    public static function send($mobile)
    {
        $ind_sms = self::isConfig();

        $config = [
            // HTTP 請求的超时時間（秒）
            'timeout' => 5.0,

            // 默認發送配置
            'default' => [
                // 網关调用策略，默認：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默認可用的發送網关
                'gateways' => [
                    'yunpian', 'aliyun',
                ],
            ],
            // 可用的網关配置
            'gateways' => [
                'aliyun' => [
                    'access_key_id' => $ind_sms['aliyun']['access_key_id'],
                    'access_key_secret' => $ind_sms['aliyun']['access_key_secret'],
                    'sign_name' => $ind_sms['aliyun']['sign'],
                ],
            ],
        ];

        $easySms = new EasySms($config);

        $sms_code = self::generateCode();
        self::generateSession($mobile,$sms_code);

        $easySms->send($mobile, [
            'template' => $ind_sms['aliyun']['tpl_id'],
            'data' => [
                'code' => $sms_code
            ],
        ]);

        return true;
    }

    private static function isConfig()
    {
        $ind_sms = Option::getList('ind_sms', 0, 'admin')['ind_sms'];
        if (!$ind_sms) {
            throw new Exception('發送失敗，短信尚未配置。');
        }
        if (!$ind_sms['aliyun'] || !$ind_sms['aliyun']['access_key_id'] || !$ind_sms['aliyun']['access_key_secret'] || !$ind_sms['aliyun']['sign'] || !$ind_sms['aliyun']['tpl_id']) {
            throw new Exception('發送失敗，短信尚未配置。');
        }

        return $ind_sms;
    }

    private static function generateCode()
    {
        $code = mt_rand(100000,999999);
        return $code;
    }

    private static function generateSession($mobile,$code)
    {
        $data = [
            'mobile' => $mobile,
            'code' => $code,
        ];

        session(apps::RESET_PASSWORD_SMS_CODE,$data);
        session(apps::RESET_PASSWORD_SMS_CODE_VALIDATE_COUNT,0);
    }

    public static function clearSession()
    {
        session(apps::RESET_PASSWORD_SMS_CODE,null);
        session(apps::RESET_PASSWORD_SMS_CODE_VALIDATE_COUNT,null);
    }
}