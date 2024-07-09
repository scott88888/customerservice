<?php

return [
    // +----------------------------------------------------------------------
    // | 应用設定
    // +----------------------------------------------------------------------
    // 默认Host地址
    'app_host'               => '',
    // 应用调试模式
    'app_debug'              => false,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式狀態
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自動绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空間
    'root_namespace'         => [],
    // 扩展函數文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 資料返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否開啟多語言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认語言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块設定
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止訪問模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认驗證器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法前缀
    'use_action_prefix'      => false,
    // 操作方法后缀
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
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参數 用于自動產生
    'url_common_param'       => false,
    // URL参數方式 0 按名稱成對解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否開啟路由
    'url_route_on'           => true,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自動转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的訪問控制器层
    'url_controller_layer'   => 'controller',
    // 表單請求类型伪装变量
    'var_method'             => '_method',
    // 表單ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表單pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否開啟請求缓存 true自動缓存 支持設定請求缓存规则
    'request_cache'          => false,
    // 請求缓存有效期
    'request_cache_expire'   => null,
    // 全局請求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板設定
    // +----------------------------------------------------------------------

    'template'               => [
        // 默认模板渲染规则 1 解析為小写+下划线 2 全部转换小写
        'auto_rule'    => 1,
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 视图基础目录，配置目录為所有模块的视图起始目录
        'view_base'    => '',
        // 当前模板的视图目录 留空為自動取得
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签開始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签開始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [],
    // 默认跳转頁面對应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及錯誤設定
    // +----------------------------------------------------------------------

    // 异常頁面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 錯誤显示訊息,非调试模式有效
    'error_message'          => '頁面錯誤！請稍后再试～',
    // 显示錯誤訊息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',
    // 是否记录trace訊息到日志
    'record_trace'           => false,

    // +----------------------------------------------------------------------
    // | 日志設定
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace設定 開啟 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存設定
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱動方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会話設定
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的送出变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱動方式 支持redis memcache memcached
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
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly設定
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    // +----------------------------------------------------------------------
    // | 資料库設定
    // +----------------------------------------------------------------------

    'database'               => [
        // 資料库类型
        'type'            => 'mysql',
        // 資料库連結DSN配置
        'dsn'             => '',
        // 服务器地址
        'hostname'        => '127.0.0.1',
        // 資料库名
        'database'        => '',
        // 資料库使用者名稱
        'username'        => 'root',
        // 資料库密碼
        'password'        => '',
        // 資料库連結端口
        'hostport'        => '',
        // 資料库連結参數
        'params'          => [],
        // 資料库编碼默认采用utf8
        'charset'         => 'utf8',
        // 資料库表前缀
        'prefix'          => '',
        // 資料库调试模式
        'debug'           => false,
        // 資料库部署方式:0 集中式(單一服务器),1 分布式(主从服务器)
        'deploy'          => 0,
        // 資料库读写是否分离 主从式有效
        'rw_separate'     => false,
        // 读写分离后 主服务器數量
        'master_num'      => 1,
        // 指定从服务器序号
        'slave_no'        => '',
        // 是否严格檢查字段是否存在
        'fields_strict'   => true,
        // 資料集返回类型
        'resultset_type'  => 'array',
        // 自動写入時間戳字段
        'auto_timestamp'  => false,
        // 時間字段取出后的默认時間格式
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
