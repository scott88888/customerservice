<?php


namespace app\admin\controller;

use app\admin\model\Admins;
use app\platform\enum\apps;
use app\platform\model\Business;
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
        $this->business_id = $this->request->param('business_id',Cookie::get('YMWL_APP_FLAG'));

       if( !empty($this->business_id) ){
           Cookie::set('YMWL_APP_FLAG',$this->business_id);
       }
        $this->assign('business_id',$this->business_id);
    }

    /**
     * 登入首頁.
     *
     * @return string
     */
    public function index()
    {
        $this->redirect('service/index/index');
    }

    /**
     * 注册頁面.
     *
     * @return mixed
     */
    private function sign()
    {
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
     * 注册驗證碼.
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
//        if(!isset($post['username']) || !isset($post['password']) || !isset($post['business_id'])){
        if(!isset($post['username']) || !isset($post['password'])){
          $this->error('参數不完整!', url("/admin/login/index"));
        }

        $post['user_name'] =htmlspecialchars($post['username']);

            $post["password"] =htmlspecialchars($post['password']);
            unset($post['username']);

            $result = $this->validate($post, 'Login');
            if ($result !== true) {
                $this->error($result);
            }
            // 取得訊息 根據$post['username'] 的資料 来做條件 取得整條訊息
//                        ->where('business_id',$post['business_id'])
           /* $admin = Admins::table("wolive_service")
                ->where('user_name', $post['user_name'])
                ->find();
            if (!$admin) {
                $this->error("使用者不存在");
            }*/
            // 密碼檢查

            $pass = md5($post['user_name'] . "hjkj" . $post['password']);

            $admin = Admins::table("wolive_service")
                ->where('user_name', $post['user_name'])
                ->where('password', $pass)
                ->find();

            if (!$admin) {

                $this->error('登入使用者名稱或密碼錯誤');
            }

            // 取得登入資料

            $login = $admin->getData();

            // 删掉登入使用者的敏感訊息
            unset($login['password']);

            $res = Admins::table('wolive_service')->where('service_id', $login['service_id'])->update(['state' => 'online']);

//            $data = Admins::table('wolive_service')->where('service_id', $login['service_id'])->find();



        $_SESSION['Msg'] = $login;
        $business = Business::get($_SESSION['Msg']['business_id']);
        $_SESSION['Msg']['business'] = $business->getData();

        $common =new Common();
        $expire=7*24*60*60;
        $service_token = $common->cpEncode($login['user_name'],AIKF_SALT,$expire);
        Cookie::set('service_token', $service_token, $expire);

        $ismoblie =$common->isMobile();

        if($ismoblie){
          
          $this->success('登入成功', url("mobile/admin/index"));
        }else{

          $this->success('登入成功', url("service/Index/index"));
        }
        
    }

    /**
     * 退出登入 并清除session.
     *
     * @return void
     */
    public function logout()
    {
        Cookie::delete('service_token');
      if(isset($_SESSION['Msg'])){
               $login = $_SESSION['Msg'];
            // 更改狀態
          Cookie::delete('service_token');
          setCookie("cu_com", "", time() - 60);
          $_SESSION['Msg'] = null;
        }
        $this->redirect(url('service/login/index',['business_id'=>$this->request->param('business_id')]));
           
    }

    /**
     * socket_auth 驗證
     * [auth description]
     * @return [type] [description]
     */
     public function auth(){

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
        $data= $pusher->socket_auth($_POST['channel_name'], $_POST['socket_id']);
        return $data;  
    }

}
