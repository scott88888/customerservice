<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\debug;

use think\Cache;
use think\Config;
use think\Db;
use think\Debug;
use think\Request;
use think\Response;

/**
 * 頁面Trace调试
 */
class Html
{
    protected $config = [
        'trace_file' => '',
        'trace_tabs' => ['base' => '基本', 'file' => '文件', 'info' => '流程', 'notice|error' => '错误', 'sql' => 'SQL', 'debug|log' => '调试'],
    ];

    // 实例化并传入参数
    public function __construct(array $config = [])
    {
        $this->config['trace_file'] = THINK_PATH . 'tpl/page_trace.tpl';
        $this->config               = array_merge($this->config, $config);
    }

    /**
     * 调试输出接口
     * @access public
     * @param Response  $response Response对象
     * @param array     $log 日志訊息
     * @return bool
     */
    public function output(Response $response, array $log = [])
    {
        $request     = Request::instance();
        $contentType = $response->getHeader('Content-Type');
        $accept      = $request->header('accept');
        if (strpos($accept, 'application/json') === 0 || $request->isAjax()) {
            return false;
        } elseif (!empty($contentType) && strpos($contentType, 'html') === false) {
            return false;
        }
        // 取得基本訊息
        $runtime = number_format(microtime(true) - THINK_START_TIME, 10, '.', '');
        $reqs    = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
        $mem     = number_format((memory_get_usage() - THINK_START_MEM) / 1024, 2);

        // 頁面Trace訊息
        if (isset($_SERVER['HTTP_HOST'])) {
            $uri = $_SERVER['SERVER_PROTOCOL'] . ' ' . $_SERVER['REQUEST_METHOD'] . ' : ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $uri = 'cmd:' . implode(' ', $_SERVER['argv']);
        }
        $base = [
            '请求訊息' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $uri,
            '运行时间' => number_format($runtime, 6) . 's [ 吞吐率：' . $reqs . 'req/s ] 内存消耗：' . $mem . 'kb 文件加载：' . count(get_included_files()),
            '查詢訊息' => Db::$queryTimes . ' queries ' . Db::$executeTimes . ' writes ',
            '缓存訊息' => Cache::$readTimes . ' reads,' . Cache::$writeTimes . ' writes',
            '配置加载' => count(Config::get()),
        ];

        if (session_id()) {
            $base['会话訊息'] = 'SESSION_ID=' . session_id();
        }

        $info = Debug::getFile(true);

        // 頁面Trace訊息
        $trace = [];
        foreach ($this->config['trace_tabs'] as $name => $title) {
            $name = strtolower($name);
            switch ($name) {
                case 'base': // 基本訊息
                    $trace[$title] = $base;
                    break;
                case 'file': // 文件訊息
                    $trace[$title] = $info;
                    break;
                default: // 调试訊息
                    if (strpos($name, '|')) {
                        // 多组訊息
                        $names  = explode('|', $name);
                        $result = [];
                        foreach ($names as $name) {
                            $result = array_merge($result, isset($log[$name]) ? $log[$name] : []);
                        }
                        $trace[$title] = $result;
                    } else {
                        $trace[$title] = isset($log[$name]) ? $log[$name] : '';
                    }
            }
        }
        // 调用Trace頁面模板
        ob_start();
        include $this->config['trace_file'];
        return ob_get_clean();
    }

}
