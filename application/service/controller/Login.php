<?php


namespace app\service\controller;

use app\service\model\Admins;
use app\service\model\AdminLog;
use app\service\model\Business;
use think\Controller;
use think\captcha\Captcha;
use think\config;
use app\Common;
use app\extra\push\Pusher;
use think\Cookie;


/**
 * 登入控制器.
 */
class Login extends Controller
{
    private $business_id = null;

    public function _initialize()
    {
        $this->business_id = $this->request->param('business_id', Cookie::get('AIKF_APP_FLAG'));
        if (!empty($this->business_id)) Cookie::set('AIKF_APP_FLAG', $this->business_id);
        $this->assign('business_id', $this->business_id);
    }

    /**
     * 登入首頁.
     *
     * @return string
     */
    public function index()
    {
        $token = Cookie::get('service_token');
        if ($token||(isset($_SESSION['Msg'])&&!empty($_SESSION['Msg']))) $this->redirect(url('service/index/index'));
        // 未登入，呈现登入頁面.
        $params = [];
        $goto = $this->request->get('goto', '');
        if ($goto) $params['goto'] = urlencode($goto);
        $business = [];
        if ($this->business_id) $business = Business::get($this->business_id);
        $this->assign('business', $business);
        $this->assign('submit', url('check', $params));
        return $this->fetch();
    }


    /**
     * 驗證碼.
     *
     * @return \think\Response
     */
    public function captcha()
    {
        $captcha = new Captcha(Config::get('captcha'));
        ob_clean();
        return $captcha->entry('admin_login');
    }

    /**
     * 註冊驗證碼.
     *
     * @return \think\Response
     */
    public function captchaForAdmin()
    {
        $captcha = new Captcha(Config::get('captcha'));
        return $captcha->entry('admin_regist');
    }

    /**
     * 登入檢查.
     *
     * @return void
     */
    public function check()
    {
        $post = $this->request->post();
        if (!isset($post['username']) || !isset($post['password'])) $this->error('参數不完整!', url("/service/login/index"));
        $post['user_name'] = htmlspecialchars($post['username']);
        $post["password"] = htmlspecialchars($post['password']);
        unset($post['username']);
        $result = $this->validate($post, 'Login');
        if ($result !== true) $this->error($result);
        $pass = md5($post['user_name'] . "hjkj" . $post['password']);
        $admin = Admins::table("wolive_service")
            ->where('user_name', $post['user_name'])
            ->where('password', $pass)
            ->find();
        if (!$admin) {
            $this->record_log('登入失敗');
            $this->error('登入使用者名稱或密碼錯誤');
        }
        // 取得登入資料
        $login = $admin->getData();
        // 删掉登入使用者的敏感訊息
        unset($login['password']);
        $res = Admins::table('wolive_service')->where('service_id', $login['service_id'])->update(['state' => 'online']);
        $_SESSION['Msg'] = $login;
        $business = Business::get($_SESSION['Msg']['business_id']);
        $_SESSION['Msg']['business'] = $business->getData();
        $common = new Common();
        $expire = 7 * 24 * 60 * 60;
        $service_token = $common->cpEncode($login['user_name'], AIKF_SALT, $expire);
        Cookie::set('service_token', $service_token, $expire);
        $ismoblie = $common->isMobile();
        $this->record_log('登入成功');
        if ($ismoblie) {
            $this->success('登入成功', url("mobile/admin/index"));
        } else {
            $this->success('登入成功', url("service/Index/index"));
        }
    }

    public function reg_check(){
        if(!config('open_reg')) $this->error('禁止商户註冊');
        $post = $this->request->post();
        if (!isset($post['username']) || !isset($post['password'])) $this->error('参數不完整!', url("/service/login/reg"));
        $post['user_name'] = htmlspecialchars($post['username']);
        $post["password"] = htmlspecialchars($post['password']);
        unset($post['username']);
        $result = $this->validate($post, 'Login');
        if ($result !== true) $this->error($result);
        $business = Business::get(['business_name' => $post['user_name']]);
        if ($business) $this->error('商户名稱已存在');
        $add = array(
            'business_name' => $post['user_name'],
            'max_count' => "3",
            'expire_time' => time()+86400*config('default_reg_day'),
            'password' => $post['password'],
        );
        if(Business::addBusiness($add)) $this->success('註冊成功！', url("service/login/index"));
        $this->error('操作失敗！');
    }

    private function record_log($info)
    {
        $data = [
            'uid' => isset($_SESSION['Msg']['service_id']) ?$_SESSION['Msg']['service_id']: 0,
            'info' => $info,
            'ip' => $this->request->ip(),
            'user_agent' => $this->request->server('HTTP_USER_AGENT'),
            'create_time' => time(),
        ];
        AdminLog::table('wolive_admin_log')->insert($data);
    }

    /**
     * 退出登入 並清除session.
     *
     * @return void
     */
    public function logout()
    {
        Cookie::delete('service_token');
        if (isset($_SESSION['Msg'])) {
            $login = $_SESSION['Msg'];
            // 更改狀態
            Cookie::delete('service_token');
            setCookie("cu_com", "", time() - 60);
            $_SESSION['Msg'] = null;
        }
        $this->success('退出成功', url("service/Login/index"));

    }

    /**
     * socket_auth 驗證
     * [auth description]
     * @return [type] [description]
     */
    public function auth()
    {
        $sarr = parse_url(ahost);
        if ($sarr['scheme'] == 'https') {
            $state = true;
        } else {
            $state = false;
        }
        $app_key = app_key;
        $app_secret = app_secret;
        $app_id = app_id;
        $options = array(
            'encrypted' => $state
        );
        $host = ahost;
        $port = aport;
        $pusher = new Pusher(
            $app_key,
            $app_secret,
            $app_id,
            $options,
            $host,
            $port
        );
        $data = $pusher->socket_auth($_POST['channel_name'], $_POST['socket_id']);
        return $data;
    }

    public function reg(){
        if(isset($_SESSION['Msg'])&&!empty($_SESSION['Msg'])) $this->redirect(url('/service/index'));
        if(!config('open_reg')) $this->error('禁止商户註冊');
        return $this->fetch();
    }
}
