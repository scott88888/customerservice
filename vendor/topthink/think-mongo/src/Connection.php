<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\mongo;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\AuthenticationException;
use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Exception\InvalidArgumentException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query as MongoQuery;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use think\Collection;
use think\Db;
use think\Debug;
use think\Exception;
use think\Log;
use think\mongo\Query as Query;

/**
 * Mongo資料库驱動
 */
class Connection
{
    protected $dbName = ''; // dbName
    /** @var string 当前SQL指令 */
    protected $queryStr = '';
    // 查詢資料集类型
    protected $resultSetType = 'array';
    // 查詢資料类型
    protected $typeMap = 'array';
    protected $mongo; // MongoDb Object
    protected $cursor; // MongoCursor Object

    // 监听回调
    protected static $event = [];
    /** @var PDO[] 資料库連結ID 支持多个連結 */
    protected $links = [];
    /** @var PDO 当前連結ID */
    protected $linkID;
    protected $linkRead;
    protected $linkWrite;

    // 返回或者影响记录數
    protected $numRows = 0;
    // 錯誤訊息
    protected $error = '';
    // 查詢對象
    protected $query = [];
    // 查詢参數
    protected $options = [];
    // 資料库連結参數配置
    protected $config = [
        // 資料库类型
        'type'           => '',
        // 服务器地址
        'hostname'       => '',
        // 資料库名
        'database'       => '',
        // 使用者名稱
        'username'       => '',
        // 密碼
        'password'       => '',
        // 端口
        'hostport'       => '',
        // 連結dsn
        'dsn'            => '',
        // 資料库連結参數
        'params'         => [],
        // 資料库编碼默认采用utf8
        'charset'        => 'utf8',
        // 主键名
        'pk'             => '_id',
        // 資料库表前缀
        'prefix'         => '',
        // 資料库调试模式
        'debug'          => false,
        // 資料库部署方式:0 集中式(單一服务器),1 分布式(主从服务器)
        'deploy'         => 0,
        // 資料库读写是否分离 主从式有效
        'rw_separate'    => false,
        // 读写分离后 主服务器數量
        'master_num'     => 1,
        // 指定从服务器序号
        'slave_no'       => '',
        // 是否严格檢查字段是否存在
        'fields_strict'  => true,
        // 資料集返回类型
        'resultset_type' => 'array',
        // 自動写入時間戳字段
        'auto_timestamp' => false,
        // 是否需要进行SQL性能分析
        'sql_explain'    => false,
        // 是否_id转换為id
        'pk_convert_id'  => false,
        // typeMap
        'type_map'       => ['root' => 'array', 'document' => 'array'],
        // Query對象
        'query'          => '\\think\\mongo\\Query',
    ];

    /**
     * 架构函數 读取資料库配置訊息
     * @access public
     * @param array $config 資料库配置數组
     */
    public function __construct(array $config = [])
    {
        if (!class_exists('\MongoDB\Driver\Manager')) {
            throw new Exception('require mongodb > 1.0');
        }
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 連結資料库方法
     * @access public
     * @param array         $config 連結参數
     * @param integer       $linkNum 連結序号
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function connect(array $config = [], $linkNum = 0)
    {
        if (!isset($this->links[$linkNum])) {
            if (empty($config)) {
                $config = $this->config;
            } else {
                $config = array_merge($this->config, $config);
            }
            $this->dbName  = $config['database'];
            $this->typeMap = $config['type_map'];
            // 记录資料集返回类型
            if (isset($config['resultset_type'])) {
                $this->resultSetType = $config['resultset_type'];
            }
            if ($config['pk_convert_id'] && '_id' == $config['pk']) {
                $this->config['pk'] = 'id';
            }
            $host = 'mongodb://' . ($config['username'] ? "{$config['username']}" : '') . ($config['password'] ? ":{$config['password']}@" : '') . $config['hostname'] . ($config['hostport'] ? ":{$config['hostport']}" : '') . '/' . ($config['database'] ? "{$config['database']}" : '');
            if ($config['debug']) {
                $startTime = microtime(true);
            }
            $this->links[$linkNum] = new Manager($host, $this->config['params']);
            if ($config['debug']) {
                // 记录資料库連結訊息
                Log::record('[ DB ] CONNECT:[ UseTime:' . number_format(microtime(true) - $startTime, 6) . 's ] ' . $config['dsn'], 'sql');
            }
        }
        return $this->links[$linkNum];
    }

    /**
     * 建立指定模型的查詢對象
     * @access public
     * @param string $model 模型类名稱
     * @param string $queryClass 查詢對象类名
     * @return Query
     */
    public function model($model, $queryClass = '')
    {
        if (!isset($this->query[$model])) {
            $class               = $queryClass ?: $this->config['query'];
            $this->query[$model] = new $class($this, $model);
        }
        return $this->query[$model];
    }

    /**
     * 调用Query类的查詢方法
     * @access public
     * @param string    $method 方法名稱
     * @param array     $args 调用参數
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!isset($this->query['database'])) {
            $class                   = $this->config['query'];
            $this->query['database'] = new $class($this);
        }
        return call_user_func_array([$this->query['database'], $method], $args);
    }

    /**
     * 取得資料库的配置参數
     * @access public
     * @param string $config 配置名稱
     * @return mixed
     */
    public function getConfig($config = '')
    {
        return $config ? $this->config[$config] : $this->config;
    }

    /**
     * 設定資料库的配置参數
     * @access public
     * @param string    $config 配置名稱
     * @param mixed     $value 配置值
     * @return void
     */
    public function setConfig($config, $value)
    {
        $this->config[$config] = $value;
    }

    /**
     * 取得Mongo Manager對象
     * @access public
     * @return Manager|null
     */
    public function getMongo()
    {
        if (!$this->mongo) {
            return null;
        } else {
            return $this->mongo;
        }
    }

    /**
     * 設定/取得当前操作的database
     * @access public
     * @param string  $db db
     * @throws Exception
     */
    public function db($db = null)
    {
        if (is_null($db)) {
            return $this->dbName;
        } else {
            $this->dbName = $db;
        }
    }

    /**
     * 执行查詢
     * @access public
     * @param string            $namespace 当前查詢的collection
     * @param MongoQuery        $query 查詢對象
     * @param ReadPreference    $readPreference readPreference
     * @param string|bool       $class 返回的資料集类型
     * @param string|array      $typeMap 指定返回的typeMap
     * @return mixed
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     */
    public function query($namespace, MongoQuery $query, ReadPreference $readPreference = null, $class = false, $typeMap = null)
    {
        $this->initConnect(false);
        Db::$queryTimes++;

        if (false === strpos($namespace, '.')) {
            $namespace = $this->dbName . '.' . $namespace;
        }
        if ($this->config['debug'] && !empty($this->queryStr)) {
            // 记录执行指令
            $this->queryStr = 'db' . strstr($namespace, '.') . '.' . $this->queryStr;
        }
        $this->debug(true);
        $this->cursor = $this->mongo->executeQuery($namespace, $query, $readPreference);
        $this->debug(false);
        return $this->getResult($class, $typeMap);
    }

    /**
     * 执行指令
     * @access public
     * @param Command           $command 指令
     * @param string            $dbName 当前資料库名
     * @param ReadPreference    $readPreference readPreference
     * @param string|bool       $class 返回的資料集类型
     * @param string|array      $typeMap 指定返回的typeMap
     * @return mixed
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     */
    public function command(Command $command, $dbName = '', ReadPreference $readPreference = null, $class = false, $typeMap)
    {
        $this->initConnect(false);
        Db::$queryTimes++;

        $this->debug(true);
        $dbName = $dbName ?: $this->dbName;
        if ($this->config['debug'] && !empty($this->queryStr)) {
            $this->queryStr = 'db.' . $dbName . '.' . $this->queryStr;
        }
        $this->cursor = $this->mongo->executeCommand($dbName, $command, $readPreference);
        $this->debug(false);
        return $this->getResult($class, $typeMap);

    }

    /**
     * 获得資料集
     * @access protected
     * @param bool|string       $class true 返回Mongo cursor對象 字符串用于指定返回的类名
     * @param string|array      $typeMap 指定返回的typeMap
     * @return mixed
     */
    protected function getResult($class = '', $typeMap = null)
    {
        if (true === $class) {
            return $this->cursor;
        }
        // 設定结果資料类型
        if (is_null($typeMap)) {
            $typeMap = $this->typeMap;
        }
        $typeMap = is_string($typeMap) ? ['root' => $typeMap] : $typeMap;
        $this->cursor->setTypeMap($typeMap);

        // 取得資料集
        $result = $this->cursor->toArray();
        if ($this->getConfig('pk_convert_id')) {
            // 转换ObjectID 字段
            foreach ($result as &$data) {
                $this->convertObjectID($data);
            }
        }
        $this->numRows = count($result);
        if (!empty($class)) {
            // 返回指定資料集對象类
            $result = new $class($result);
        } elseif ('collection' == $this->resultSetType) {
            // 返回資料集Collection對象
            $result = new Collection($result);
        }
        return $result;
    }

    /**
     * ObjectID处理
     * @access public
     * @param array     $data
     * @return void
     */
    private function convertObjectID(&$data)
    {
        if (isset($data['_id'])) {
            $data['id'] = $data['_id']->__toString();
            unset($data['_id']);
        }
    }

    /**
     * 执行写操作
     * @access public
     * @param string        $namespace
     * @param BulkWrite     $bulk
     * @param WriteConcern  $writeConcern
     *
     * @return WriteResult
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     * @throws ConnectionException
     * @throws RuntimeException
     * @throws BulkWriteException
     */
    public function execute($namespace, BulkWrite $bulk, WriteConcern $writeConcern = null)
    {
        $this->initConnect(true);
        Db::$executeTimes++;
        if (false === strpos($namespace, '.')) {
            $namespace = $this->dbName . '.' . $namespace;
        }
        if ($this->config['debug'] && !empty($this->queryStr)) {
            // 记录执行指令
            $this->queryStr = 'db' . strstr($namespace, '.') . '.' . $this->queryStr;
        }
        $this->debug(true);
        $writeResult = $this->mongo->executeBulkWrite($namespace, $bulk, $writeConcern);
        $this->debug(false);
        $this->numRows = $writeResult->getMatchedCount();
        return $writeResult;
    }

    /**
     * 資料库日志记录（仅供参考）
     * @access public
     * @param string $type 类型
     * @param mixed  $data 資料
     * @param array  $options 参數
     * @return void
     */
    public function log($type, $data, $options = [])
    {
        if (!$this->config['debug']) {
            return;
        }
        if (is_array($data)) {
            array_walk_recursive($data, function (&$value) {
                if ($value instanceof ObjectID) {
                    $value = $value->__toString();
                }
            });
        }
        switch (strtolower($type)) {
            case 'find':
                $this->queryStr = $type . '(' . ($data ? json_encode($data) : '') . ')';
                if (isset($options['sort'])) {
                    $this->queryStr .= '.sort(' . json_encode($options['sort']) . ')';
                }
                if (isset($options['limit'])) {
                    $this->queryStr .= '.limit(' . $options['limit'] . ')';
                }
                $this->queryStr .= ';';
                break;
            case 'insert':
            case 'remove':
                $this->queryStr = $type . '(' . ($data ? json_encode($data) : '') . ');';
                break;
            case 'update':
                $this->queryStr = $type . '(' . json_encode($options) . ',' . json_encode($data) . ');';
                break;
            case 'cmd':
                $this->queryStr = $data . '(' . json_encode($options) . ');';
                break;
        }
        $this->options = $options;
    }

    /**
     * 取得执行的指令
     * @access public
     * @return string
     */
    public function getQueryStr()
    {
        return $this->queryStr;
    }

    /**
     * 监听SQL执行
     * @access public
     * @param callable $callback 回调方法
     * @return void
     */
    public function listen($callback)
    {
        self::$event[] = $callback;
    }

    /**
     * 触发SQL事件
     * @access protected
     * @param string    $sql 语句
     * @param float     $runtime 运行時間
     * @param array     $options 参數
     * @return bool
     */
    protected function trigger($sql, $runtime, $options = [])
    {
        if (!empty(self::$event)) {
            foreach (self::$event as $callback) {
                if (is_callable($callback)) {
                    call_user_func_array($callback, [$sql, $runtime, $options]);
                }
            }
        } else {
            // 未注册监听则记录到日志中
            Log::record('[ Mongo ] ' . $sql . ' [ RunTime:' . $runtime . 's ]', 'sql');
        }
    }

    /**
     * 資料库调试 记录当前SQL及分析性能
     * @access protected
     * @param boolean $start 调试開始标记 true 開始 false 结束
     * @param string  $sql 执行的SQL语句 留空自動取得
     * @return void
     */
    protected function debug($start, $sql = '')
    {
        if (!empty($this->config['debug'])) {
            // 開啟資料库调试模式
            if ($start) {
                Debug::remark('queryStartTime', 'time');
            } else {
                // 记录操作结束時間
                Debug::remark('queryEndTime', 'time');
                $runtime = Debug::getRangeTime('queryStartTime', 'queryEndTime');
                $sql     = $sql ?: $this->queryStr;
                // SQL监听
                $this->trigger($sql, $runtime, $this->options);
            }
        }
    }

    /**
     * 释放查詢结果
     * @access public
     */
    public function free()
    {
        $this->cursor = null;
    }

    /**
     * 關閉資料库
     * @access public
     */
    public function close()
    {
        if ($this->mongo) {
            $this->mongo  = null;
            $this->cursor = null;
        }
    }

    /**
     * 初始化資料库連結
     * @access protected
     * @param boolean $master 是否主服务器
     * @return void
     */
    protected function initConnect($master = true)
    {
        if (!empty($this->config['deploy'])) {
            // 采用分布式資料库
            if ($master) {
                if (!$this->linkWrite) {
                    $this->linkWrite = $this->multiConnect(true);
                }
                $this->mongo = $this->linkWrite;
            } else {
                if (!$this->linkRead) {
                    $this->linkRead = $this->multiConnect(false);
                }
                $this->mongo = $this->linkRead;
            }
        } elseif (!$this->mongo) {
            // 默认單資料库
            $this->mongo = $this->connect();
        }
    }

    /**
     * 連結分布式服务器
     * @access protected
     * @param boolean $master 主服务器
     * @return PDO
     */
    protected function multiConnect($master = false)
    {
        $_config = [];
        // 分布式資料库配置解析
        foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn', 'charset'] as $name) {
            $_config[$name] = explode(',', $this->config[$name]);
        }

        // 主服务器序号
        $m = floor(mt_rand(0, $this->config['master_num'] - 1));

        if ($this->config['rw_separate']) {
            // 主从式采用读写分离
            if ($master) // 主服务器写入
            {
                $r = $m;
            } elseif (is_numeric($this->config['slave_no'])) {
                // 指定服务器读
                $r = $this->config['slave_no'];
            } else {
                // 读操作連結从服务器 每次随机連結的資料库
                $r = floor(mt_rand($this->config['master_num'], count($_config['hostname']) - 1));
            }
        } else {
            // 读写操作不区分服务器 每次随机連結的資料库
            $r = floor(mt_rand(0, count($_config['hostname']) - 1));
        }
        $dbConfig = [];
        foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn', 'charset'] as $name) {
            $dbConfig[$name] = isset($_config[$name][$r]) ? $_config[$name][$r] : $_config[$name][0];
        }
        return $this->connect($dbConfig, $r);
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        // 释放查詢
        $this->free();

        // 關閉連結
        $this->close();
    }
}
