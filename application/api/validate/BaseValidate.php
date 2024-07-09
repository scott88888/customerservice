<?php
/**
 * Created by PhpStorm.
 * User: Andy
 * Date: 2020/1/14
 * Time: 10:08
 */

namespace app\api\validate;
use app\common\exception\ApiException;
use think\Request;
use think\Validate;

/**
 * Class BaseValidate
 * 驗證类的基类
 */
class BaseValidate extends Validate
{
    /**
     * 检测所有客户端发来的参數是否符合驗證类规则
     * 基类定义了很多自訂驗證方法
     * 这些自訂驗證方法其实，也可以直接调用
     * @throws ParameterException
     * @return true
     */
    public function goCheck()
    {
        //必须設定contetn-type:application/json
        $request = Request::instance();
        $params = $request->param();

        if (!$this->check($params)) {
            throw new ApiException(
                [
                    // $this->error有一个問題，并不是一定返回數组，需要判断
                    'msg' => is_array($this->error) ? implode(
                        ';', $this->error) : $this->error,
                ]);
        }
        return true;
    }

    protected function isNotEmpty($value, $rule='', $data='', $field='')
    {
        if (empty($value)) {
            return $field . '不允许為空';
        } else {
            return true;
        }
    }
}