<?php

namespace app\common\lib;

/**
 * 随机產生类
 */
class Random
{

    /**
     * 產生數字和字母
     *
     * @param int $len 長度
     * @return string
     */
    public static function alnum($len = 6)
    {
        return self::build('alnum', $len);
    }

    /**
     * 仅產生字符
     *
     * @param int $len 長度
     * @return string
     */
    public static function alpha($len = 6)
    {
        return self::build('alpha', $len);
    }

    /**
     * 產生指定長度的随机數字
     *
     * @param int $len 長度
     * @return string
     */
    public static function numeric($len = 4)
    {
        return self::build('numeric', $len);
    }

    /**
     * 數字和字母组合的随机字符串
     *
     * @param int $len 長度
     * @return string
     */
    public static function nozero($len = 4)
    {
        return self::build('nozero', $len);
    }

    /**
     * 能用的随机數產生
     * @param string $type 类型 alpha/alnum/numeric/nozero/unique/md5/encrypt/sha1
     * @param int $len 長度
     * @return string
     */
    public static function build($type = 'alnum', $len = 8)
    {
        switch ($type)
        {
            case 'alpha':
            case 'alnum':
            case 'numeric':
            case 'nozero':
                switch ($type)
                {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric':
                        $pool = '0123456789';
                        break;
                    case 'nozero':
                        $pool = '123456789';
                        break;
                }
                return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
            case 'unique':
            case 'md5':
                return md5(uniqid(mt_rand()));
            case 'encrypt':
            case 'sha1':
                return sha1(uniqid(mt_rand(), TRUE));
        }
    }

    /**
     * 根據數组元素的概率获得键名
     *
     * @param array $ps array('p1'=>20, 'p2'=>30, 'p3'=>50);
     * @param array $num 默认為1,即随机出来的數量
     * @param array $unique 默认為true,即当num>1时,随机出的數量是否唯一
     * @return mixed 当num為1时返回键名,反之返回一维數组
     */
    public static function lottery($ps, $num = 1, $unique = true)
    {
        if (!$ps)
        {
            return $num == 1 ? '' : [];
        }
        if ($num >= count($ps) && $unique)
        {
            $res = array_keys($ps);
            return $num == 1 ? $res[0] : $res;
        }
        $max_exp = 0;
        $res = [];
        foreach ($ps as $key => $value)
        {
            $value = substr($value, 0, stripos($value, ".") + 6);
            $exp = strlen(strchr($value, '.')) - 1;
            if ($exp > $max_exp)
            {
                $max_exp = $exp;
            }
        }
        $pow_exp = pow(10, $max_exp);
        if ($pow_exp > 1)
        {
            reset($ps);
            foreach ($ps as $key => $value)
            {
                $ps[$key] = $value * $pow_exp;
            }
        }
        $pro_sum = array_sum($ps);
        if ($pro_sum < 1)
        {
            return $num == 1 ? '' : [];
        }
        for ($i = 0; $i < $num; $i++)
        {
            $rand_num = mt_rand(1, $pro_sum);
            reset($ps);
            foreach ($ps as $key => $value)
            {
                if ($rand_num <= $value)
                {
                    break;
                }
                else
                {
                    $rand_num -= $value;
                }
            }
            if ($num == 1)
            {
                $res = $key;
                break;
            }
            else
            {
                $res[$i] = $key;
            }
            if ($unique)
            {
                $pro_sum -= $value;
                unset($ps[$key]);
            }
        }
        return $res;
    }

    /**
     * 取得全球唯一標識
     * @return string
     */
    public static function uuid()
    {
        return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

}
