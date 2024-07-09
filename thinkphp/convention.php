<?php

return [
    // +----------------------------------------------------------------------
    // | 應用設定
    // +----------------------------------------------------------------------
    // 默認Host地址
    'app_host'               => '',
    // 應用调试模式
    'app_debug'              => false,
    // 應用Trace
    'app_trace'              => false,
    // 應用模式狀態
    'app_status'             => '',
    // 是否支援多模組
    'app_multi_module'       => true,
    // 入口自動绑定模組
    'auto_bind_module'       => false,
    // 註冊的根命名空間
    'root_namespace'         => [],
    // 扩展函數文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默認输出類型
    'default_return_type'    => 'html',
    // 默認AJAX 資料返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默認JSONP格式返回的處理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默認JSONP處理方法
    'var_jsonp_handler'      => 'callback',
    // 默認时区
    'default_timezone'       => 'PRC',
    // 是否開啟多語言
    'lang_switch_on'         => false,
    // 默認全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默認語言
    'default_lang'           => 'zh-cn',
    // 應用類庫後缀
    'class_suffix'           => false,
    // 控制器類後缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模組設定
    // +----------------------------------------------------------------------

    // 默認模組名
    'default_module'         => 'index',
    // 禁止訪問模組
    'deny_module_list'       => ['common'],
    // 默認控制器名
    'default_controller'     => 'Index',
    // 默認操作名
    'default_action'         => 'index',
    // 默認驗證器
    'default_validate'       => '',
    // 默認的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法前缀
    'use_action_prefix'      => false,
    // 操作方法後缀
    'action_suffix'          => '',
    // 自動搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL設定
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO取得
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // HTTPS代理標識
    'https_agent_name'       => '',
    // URL伪静态後缀
    'url_html_suffix'        => 'html',
    // URL普通方式参數 用于自動產生
    'url_common_param'       => false,
    // URL参數方式 0 按名稱成對解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否開啟路由
    'url_route_on'           => true,
    // 路由配置文件（支援配置多个）
    'route_config_file'      => ['route'],
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自動轉換URL中的控制器和操作名
    'url_convert'            => true,
    // 默認的訪問控制器層
    'url_controller_layer'   => 'controller',
    // 表單請求類型伪装变量
    'var_method'             => '_method',
    // 表單ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表單pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否開啟請求快取 true自動快取 支援設定請求快取規則
    'request_cache'          => false,
    // 請求快取有效期
    'request_cache_expire'   => null,
    // 全局請求快取排除規則
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板設定
    // +----------------------------------------------------------------------

    'template'               => [
        // 默認模板渲染規則 1 解析為小寫+下划线 2 全部轉換小寫
        'auto_rule'    => 1,
        // 模板引擎類型 支援 php think 支援扩展
        'type'         => 'Think',
        // 视圖基础目錄，配置目錄為所有模組的视圖起始目錄
        'view_base'    => '',
        // 當前模板的视圖目錄 留空為自動取得
        'view_path'    => '',
        // 模板後缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签開始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签庫标签開始标记
        'taglib_begin' => '{',
        // 标签庫标签结束标记
        'taglib_end'   => '}',
    ],

    // 视圖输出字符串内容替换
    'view_replace_str'       => [],
    // 默認跳转頁面對应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及錯誤設定
    // +----------------------------------------------------------------------

    // 异常頁面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 錯誤显示訊息,非调试模式有效
    'error_message'          => '頁面錯誤！請稍後再试～',
    // 显示錯誤訊息
    'show_error_msg'         => false,
    // 异常處理handle類 留空使用 \think\exception\Handle
    'exception_handle'       => '',
    // 是否记录trace訊息到日誌
    'record_trace'           => false,

    // +----------------------------------------------------------------------
    // | 日誌設定
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日誌记录方式，内置 file socket 支援扩展
        'type'  => 'File',
        // 日誌保存目錄
        'path'  => LOG_PATH,
        // 日誌记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace設定 開啟 app_trace 後 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支援扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 快取設定
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱動方式
        'type'   => 'File',
        // 快取保存目錄
        'path'   => CACHE_PATH,
        // 快取前缀
        'prefix' => '',
        // 快取有效期 0表示永久快取
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会話設定
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的送出变量,解决flash上傳跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱動方式 支援redis memcache memcached
        'type'           => '',
        // 是否自動開啟 SESSION
        'auto_start'     => true,
        'httponly'       => true,
        'secure'         => false,
    ],

    // +----------------------------------------------------------------------
    // | Cookie設定
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名稱前缀
        'prefix'    => '',
        // cookie 保存時間
        'expire'    => 0,
        // cookie 保存路徑
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全傳输
        'secure'    => false,
        // httponly設定
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    // +----------------------------------------------------------------------
    // | 資料庫設定
    // +----------------------------------------------------------------------

    'database'               => [
        // 資料庫類型
        'type'            => 'mysql',
        // 資料庫連結DSN配置
        'dsn'             => '',
        // 服务器地址
        'hostname'        => '127.0.0.1',
        // 資料庫名
        'database'        => '',
        // 資料庫使用者名稱
        'username'        => 'root',
        // 資料庫密碼
        'password'        => '',
        // 資料庫連結端口
        'hostport'        => '',
        // 資料庫連結参數
        'params'          => [],
        // 資料庫编碼默認采用utf8
        'charset'         => 'utf8',
        // 資料庫表前缀
        'prefix'          => '',
        // 資料庫调试模式
        'debug'           => false,
        // 資料庫部署方式:0 集中式(單一服务器),1 分布式(主从服务器)
        'deploy'          => 0,
        // 資料庫读寫是否分离 主从式有效
        'rw_separate'     => false,
        // 读寫分离後 主服务器數量
        'master_num'      => 1,
        // 指定从服务器序号
        'slave_no'        => '',
        // 是否严格檢查字段是否存在
        'fields_strict'   => true,
        // 資料集返回類型
        'resultset_type'  => 'array',
        // 自動寫入時間戳字段
        'auto_timestamp'  => false,
        // 時間字段取出後的默認時間格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain'     => false,
    ],

    //分頁配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],

    //控制台配置
    'console'                => [
        'name'    => 'Think Console',
        'version' => '0.1',
        'user'    => null,
    ],

];
