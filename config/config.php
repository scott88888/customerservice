<?php


use \think\Request;

$basename = Request::instance()->root();
if (pathinfo($basename, PATHINFO_EXTENSION) == 'php') {
    $basename = dirname($basename);
}

return [
    // +----------------------------------------------------------------------
    // | 應用設定
    // +----------------------------------------------------------------------

    // 應用命名空間
    'app_namespace' => 'app',
    // 應用调试模式
    'app_debug' => false,
    // 應用Trace
    'app_trace' => false,
    // 應用模式狀態
    'app_status' => '',
    // 是否支援多模組
    'app_multi_module' => true,
    // 入口自動绑定模組
    'auto_bind_module' => false,
    // 註冊的根命名空間
    'root_namespace' => [],
    // 扩展函數文件
    'extra_file_list' => [APP_PATH . 'helper.php', THINK_PATH . 'helper' . EXT],
    // 默認输出類型
    'default_return_type' => 'html',
    // 默認AJAX 資料返回格式,可选json xml ...
    'default_ajax_return' => 'json',
    // 默認JSONP格式返回的處理方法
    'default_jsonp_handler' => 'jsonpReturn',
    // 默認JSONP處理方法
    'var_jsonp_handler' => 'callback',
    // 默認时区
    'default_timezone' => 'PRC',
    // 是否開啟多語言
    'lang_switch_on' => false,
    // 默認全局过滤方法 用逗号分隔多个
    'default_filter' => 'htmlspecialchars,trim',
    // 默認語言
    'default_lang' => 'zh-cn',
    // 應用類庫後缀
    'class_suffix' => false,
    // 控制器類後缀
    'controller_suffix' => false,

    // +----------------------------------------------------------------------
    // | 模組設定
    // +----------------------------------------------------------------------


    // 默認模組名
    'default_module' => 'index',
    // 禁止訪問模組
    'deny_module_list' => ['common', 'platform'],
    // 默認控制器名
    'default_controller' => 'Index',
    // 默認操作名
    'default_action' => 'index',
    // 默認驗證器
    'default_validate' => '',
    // 默認的空控制器名
    'empty_controller' => 'Error',
    // 操作方法後缀
    'action_suffix' => '',
    // 自動搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL設定
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo' => 's',
    // 兼容PATH_INFO取得
    'pathinfo_fetch' => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr' => '/',
    // URL伪静态後缀
    'url_html_suffix' => 'html',
    // URL普通方式参數 用于自動產生
    'url_common_param' => false,
    // URL参數方式 0 按名稱成對解析 1 按顺序解析
    'url_param_type' => 0,
    // 是否開啟路由
    'url_route_on' => true,
    // 路由使用完整匹配
    'route_complete_match' => false,
    // 路由配置文件（支援配置多个）
    'route_config_file' => ['route'],
    // 是否强制使用路由
    'url_route_must' => false,
    // 域名部署
    'url_domain_deploy' => false,
    // 域名根，如thinkphp.cn
    'url_domain_root' => '',
    // 是否自動轉換URL中的控制器和操作名
    'url_convert' => true,
    // 默認的訪問控制器層
    'url_controller_layer' => 'controller',
    // 表單請求類型伪装变量
    'var_method' => '_method',
    // 表單ajax伪装变量
    'var_ajax' => '_ajax',
    // 表單pjax伪装变量
    'var_pjax' => '_pjax',
    // 是否開啟請求快取 true自動快取 支援設定請求快取規則
    'request_cache' => false,
    // 請求快取有效期
    'request_cache_expire' => null,

    // +----------------------------------------------------------------------
    // | 模板設定
    // +----------------------------------------------------------------------

    'template' => [
        // 模板引擎類型 支援 php think 支援扩展
        'type' => 'Think',
        // 模板路徑
        'view_path' => '',
        // 模板後缀
        'view_suffix' => 'html',
        // 模板文件名分隔符
        'view_depr' => DS,
        // 模板引擎普通标签開始标记
        'tpl_begin' => '{',
        // 模板引擎普通标签结束标记
        'tpl_end' => '}',
        // 标签庫标签開始标记
        'taglib_begin' => '{',
        // 标签庫标签结束标记
        'taglib_end' => '}',
    ],

    // 视圖输出字符串内容替换
    'view_replace_str' => [
        '__assets__' => $basename . '/assets',
        '__uploads__' => $basename . '/upload',
        '__image__' => $basename . '/assets/images',
        '__style__' => $basename . '/assets/css',
        '__script__' => $basename . '/assets/js',
        '__lkversion__' => AKF_VERSION,
        '__static__' => $basename . '/static',
        '__libs__' => $basename . '/assets/libs'
    ],
    // 默認跳转頁面對应的模板文件
    'dispatch_success_tmpl' => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl' => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及錯誤設定
    // +----------------------------------------------------------------------

    // 异常頁面的模板文件
    'exception_tmpl' => APP_PATH . 'common' . DS . 'tpl' . DS . 'think_exception.tpl',

    // 錯誤显示訊息,非调试模式有效
    'error_message' => '',
    // 显示錯誤訊息
    'show_error_msg' => true,
    // 异常處理handle類 留空使用 \think\exception\Handle
    'exception_handle' => '',

    // +----------------------------------------------------------------------
    // | 日誌設定
    // +----------------------------------------------------------------------

    'log' => [
        // 日誌记录方式，内置 file socket 支援扩展
        'type' => 'File',
        // 日誌保存目錄
        'path' => LOG_PATH,
        // 日誌记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace設定 開啟 app_trace 後 有效
    // +----------------------------------------------------------------------
    'trace' => [
        // 内置Html Console 支援扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 快取設定
    // +----------------------------------------------------------------------

    'cache' => [
        // 驱動方式
        'type' => 'File',
        // 快取保存目錄
        'path' => CACHE_PATH,
        // 快取前缀
        'prefix' => '',
        // 快取有效期 0表示永久快取
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会話設定
    // +----------------------------------------------------------------------

    'session' => [
        'id' => '',
        // SESSION_ID的送出变量,解决flash上傳跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix' => '',
        // 驱動方式 支援redis memcache memcached
        'type' => '',
        // 是否自動開啟 SESSION
        'auto_start' => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie設定
    // +----------------------------------------------------------------------
    'cookie' => [
        // cookie 名稱前缀
        'prefix' => '',
        // cookie 保存時間
        'expire' => 0,
        // cookie 保存路徑
        'path' => '/',
        // cookie 有效域名
        'domain' => '',
        //  cookie 启用安全傳输
        'secure' => false,
        // httponly設定
        'httponly' => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    // 分頁配置
    'paginate' => [
        'type' => 'bootstrap',
        'var_page' => 'page',
        'list_rows' => 10,
    ],

    // 驗證碼設定

    'captcha' => [
        // 驗證碼加密密钥
        'seKey' => 'AdminSystem',
        // 驗證碼字符集合
        'codeSet' => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        // 驗證碼过期時間（s）
        'expire' => 1800,
        // 驗證碼字体大小(px)
        'fontSize' => 26,
        // 是否画混淆曲线
        'useCurve' => false,
        // 是否新增杂點
        'useNoise' => true,
        // 驗證碼圖片高度
        'imageH' => 0,
        // 驗證碼圖片宽度
        'imageW' => 0,
        // 驗證碼位數
        'length' => 4,
        // 驗證碼字体，不設定随機取得
        'fontttf' => '5.ttf',
        // 背景颜色
        'bg' => [243, 251, 254],
    ],

    // 自訂錯誤頁面
    'http_exception_template' => [
        // 定義404錯誤重定向
        403 => APP_PATH . '403.html',
        404 => APP_PATH . '404.html',
        500 => APP_PATH . '500.html',
    ],

    //+----------------------------------------------------------------------
    // | Token設定
    // +----------------------------------------------------------------------
    'token' => [
        // 驱動方式
        'type' => 'Mysql',
        // 快取前缀
        'key' => 'i3d6o32wo8fvs1fvdpwens',
        // 加密方式
        'hashalgo' => 'ripemd160',
        // 快取有效期 0表示永久快取
        'expire' => 0,
    ],

    'service_lang' => 'cn',

    //註冊免费试用天數
    'default_reg_day' => 3,

    //是否開啟註冊:1開啟0關閉
    'open_reg' => 1,

    'lang' => [
        'cn' => '中文簡體',
        'tc' => '中文繁體',
        'en' => '英文',
        'vi' => '越南語',
        'rus' => '俄文',
        'id' => '印尼語',
        'th' => '泰語',
        'jp' => '日文',
        'kr' => '韓語',
        'es' => '西班牙文',
        'fra' => '法語',
        'it' => '義大利語',
        'de' => '德語',
        'pt' => '葡萄牙語',
        'ara' => '阿拉伯語',
        'dan' => '丹麥語',
        'el' => '希臘文',
        'nl' => '荷蘭語',
        'pl' => '波蘭語',
        'fin' => '芬蘭語',
    ],

    'country' => [
        'cn' => '中國',
        'tc' => '中文繁體',
        'en' => '美國',
        'vi' => '越南',
        'rus' => '俄羅斯',
        'id' => '印尼',
        'th' => '泰國',
        'jp' => '日本',
        'kr' => '韓國',
        'es' => '西班牙',
        'fra' => '法國',
        'it' => '義大利',
        'de' => '德國',
        'pt' => '葡萄牙',
        'ara' => '阿拉伯',
        'dan' => '丹麥',
        'el' => '希臘',
        'nl' => '荷蘭',
        'pl' => '波蘭',
        'fin' => '芬蘭',
    ],

    'lang_trans' => [
        'cn' => 'zh',
        'zh' => 'zh',
        'tc' => 'cht',
        'en' => 'en',
        'vi' => 'vie',
        'rus' => 'ru',
        'id' => 'id',
        'th' => 'th',
        'jp' => 'jp',
        'kr' => 'kor',
        'es' => 'spa',
        'fra' => 'fra',
        'it' => 'it',
        'de' => 'de',
        'pt' => 'pt',
        'ara' => 'ara',
        'dan' => 'dan',
        'el' => 'el',
        'nl' => 'nl',
        'pl' => 'pl',
        'fin' => 'fin',
    ],

    'lang_trans_g' => [
        'cn' => 'zh',
        'zh' => 'zh',
        'tc' => 'cht',
        'en' => 'en',
        'vi' => 'vie',
        'rus' => 'ru',
        'id' => 'id',
        'th' => 'th',
        'jp' => 'ja',
        'kr' => 'kor',
        'es' => 'spa',
        'fra' => 'fr',
        'it' => 'it',
        'de' => 'de',
        'pt' => 'pt',
        'ara' => 'ara',
        'dan' => 'dan',
        'el' => 'el',
        'nl' => 'nl',
        'pl' => 'pl',
        'fin' => 'fin',
    ],
];