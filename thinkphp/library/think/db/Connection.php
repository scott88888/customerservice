<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\db;

use PDO;
use PDOStatement;
use think\Db;
use think\db\exception\BindParamException;
use think\Debug;
use think\Exception;
use think\exception\PDOException;
use think\Log;

/**
 * Class Connection
 * @package think
 * @method Query table(string $table) 指定資料表（含前缀）
 * @method Query name(string $name) 指定資料表（不含前缀）
 *
 */
abstract class Connection
{

    /** @var PDOStatement PDO操作實例 */
    protected $PDOStatement;

    /** @var string 當前SQL指令 */
    protected $queryStr = '';
    // 返回或者影响记录數
    protected $numRows = 0;
    // 事务指令數
    protected $transTimes = 0;
    // 錯誤訊息
    protected $error = '';

    /** @var PDO[] 資料庫連結ID 支援多个連結 */
    protected $links = [];

    /** @var PDO 當前連結ID */
    protected $linkID;
    protected $linkRead;
    protected $linkWrite;

    // 查詢结果類型
    protected $fetchType = PDO::FETCH_ASSOC;
    // 字段属性大小寫
    protected $attrCase = PDO::CASE_LOWER;
    // 监听回调
    protected static $event = [];
    // 使用Builder類
    protected $builder;
    // 資料庫連結参數配置
    protected $config = [
        // 資料庫類型
        'type'            => '',
        // 服务器地址
        'hostname'        => '',
        // 資料庫名
        'database'        => '',
        // 使用者名稱
        'username'        => '',
        // 密碼
        'password'        => '',
        // 端口
        'hostport'        => '',
        // 連結dsn
        'dsn'             => '',
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
        // 模型寫入後自動读取主服务器
        'read_master'     => false,
        // 是否严格檢查字段是否存在
        'fields_strict'   => true,
        // 資料返回類型
        'result_type'     => PDO::FETCH_ASSOC,
        // 資料集返回類型
        'resultset_type'  => 'array',
        // 自動寫入時間戳字段
        'auto_timestamp'  => false,
        // 時間字段取出後的默認時間格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain'     => false,
        // Builder類
        'builder'         => '',
        // Query類
        'query'           => '\\think\\db\\Query',
        // 是否需要断线重连
        'break_reconnect' => false,
    ];

    // PDO連結参數
    protected $params = [
        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES  => false,
    ];

    // 绑定参數
    protected $bind = [];

    /**
     * 构造函數 读取資料庫配置訊息
     * @access public
     * @param array $config 資料庫配置數组
     */
    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 取得新的查詢對象
     * @access protected
     * @return Query
     */
    protected function getQuery()
    {
        $class = $this->config['query'];
        return new $class($this);
    }

    /**
     * 取得當前連結器類對应的Builder類
     * @access public
     * @return string
     */
    public function getBuilder()
    {
        if (!empty($this->builder)) {
            return $this->builder;
        } else {
            return $this->getConfig('builder') ?: '\\think\\db\\builder\\' . ucfirst($this->getConfig('type'));
        }
    }

    /**
     * 调用Query類的查詢方法
     * @access public
     * @param string    $method 方法名稱
     * @param array     $args 调用参數
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->getQuery(), $method], $args);
    }

    /**
     * 解析pdo連結的dsn訊息
     * @access protected
     * @param array $config 連結訊息
     * @return string
     */
    abstract protected function parseDsn($config);

    /**
     * 取得資料表的字段訊息
     * @access public
     * @param string $tableName
     * @return array
     */
    abstract public function getFields($tableName);

    /**
     * 取得資料庫的表訊息
     * @access public
     * @param string $dbName
     * @return array
     */
    abstract public function getTables($dbName);

    /**
     * SQL性能分析
     * @access protected
     * @param string $sql
     * @return array
     */
    abstract protected function getExplain($sql);

    /**
     * 對返資料表字段訊息进行大小寫轉換出来
     * @access public
     * @param array $info 字段訊息
     * @return array
     */
    public function fieldCase($info)
    {
        // 字段大小寫轉換
        switch ($this->attrCase) {
            case PDO::CASE_LOWER:
                $info = array_change_key_case($info);
                break;
            case PDO::CASE_UPPER:
                $info = array_change_key_case($info, CASE_UPPER);
                break;
            case PDO::CASE_NATURAL:
            default:
                // 不做轉換
        }
        return $info;
    }

    /**
     * 取得資料庫的配置参數
     * @access public
     * @param string $config 配置名稱
     * @return mixed
     */
    public function getConfig($config = '')
    {
        return $config ? $this->config[$config] : $this->config;
    }

    /**
     * 設定資料庫的配置参數
     * @access public
     * @param string|array      $config 配置名稱
     * @param mixed             $value 配置值
     * @return void
     */
    public function setConfig($config, $value = '')
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        } else {
            $this->config[$config] = $value;
        }
    }

    /**
     * 連結資料庫方法
     * @access public
     * @param array         $config 連結参數
     * @param integer       $linkNum 連結序号
     * @param array|bool    $autoConnection 是否自動連結主資料庫（用于分布式）
     * @return PDO
     * @throws Exception
     */
    public function connect(array $config = [], $linkNum = 0, $autoConnection = false)
    {
        if (!isset($this->links[$linkNum])) {
            if (!$config) {
                $config = $this->config;
            } else {
                $config = array_merge($this->config, $config);
            }
            // 連結参數
            if (isset($config['params']) && is_array($config['params'])) {
                $params = $config['params'] + $this->params;
            } else {
                $params = $this->params;
            }
            // 记录當前字段属性大小寫設定
            $this->attrCase = $params[PDO::ATTR_CASE];

            // 資料返回類型
            if (isset($config['result_type'])) {
                $this->fetchType = $config['result_type'];
            }
            try {
                if (empty($config['dsn'])) {
                    $config['dsn'] = $this->parseDsn($config);
                }
                if ($config['debug']) {
                    $startTime = microtime(true);
                }
                $this->links[$linkNum] = new PDO($config['dsn'], $config['username'], $config['password'], $params);
                if($config['type']=='mysql'){
                    $this->links[$linkNum]->exec('SET SQL_MODE="NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"');
                }
                if ($config['debug']) {
                    // 记录資料庫連結訊息
                    Log::record('[ DB ] CONNECT:[ UseTime:' . number_format(microtime(true) - $startTime, 6) . 's ] ' . $config['dsn'], 'sql');
                }
            } catch (\PDOException $e) {
                if ($autoConnection) {
                    Log::record($e->getMessage(), 'error');
                    return $this->connect($autoConnection, $linkNum);
                } else {
                    throw $e;
                }
            }
        }
        return $this->links[$linkNum];
    }

    /**
     * 释放查詢结果
     * @access public
     */
    public function free()
    {
        $this->PDOStatement = null;
    }

    /**
     * 取得PDO對象
     * @access public
     * @return \PDO|false
     */
    public function getPdo()
    {
        if (!$this->linkID) {
            return false;
        } else {
            return $this->linkID;
        }
    }

    /**
     * 執行查詢 返回資料集
     * @access public
     * @param string        $sql sql指令
     * @param array         $bind 参數绑定
     * @param bool          $master 是否在主服务器读操作
     * @param bool          $pdo 是否返回PDO對象
     * @return mixed
     * @throws PDOException
     * @throws \Exception
     */
    public function query($sql, $bind = [], $master = false, $pdo = false)
    {
        $this->initConnect($master);
        if (!$this->linkID) {
            return false;
        }

        // 记录SQL語句
        $this->queryStr = $sql;
        if ($bind) {
            $this->bind = $bind;
        }

        Db::$queryTimes++;
        try {
            // 调试開始
            $this->debug(true);

            // 预處理
            $this->PDOStatement = $this->linkID->prepare($sql);

            // 是否為存储过程调用
            $procedure = in_array(strtolower(substr(trim($sql), 0, 4)), ['call', 'exec']);
            // 参數绑定
            if ($procedure) {
                $this->bindParam($bind);
            } else {
                $this->bindValue($bind);
            }
            // 執行查詢
            $this->PDOStatement->execute();
            // 调试结束
            $this->debug(false, '', $master);
            // 返回结果集
            return $this->getResult($pdo, $procedure);
        } catch (\PDOException $e) {
            if ($this->isBreak($e)) {
                return $this->close()->query($sql, $bind, $master, $pdo);
            }
            throw new PDOException($e, $this->config, $this->getLastsql());
        } catch (\Throwable $e) {
            if ($this->isBreak($e)) {
                return $this->close()->query($sql, $bind, $master, $pdo);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($this->isBreak($e)) {
                return $this->close()->query($sql, $bind, $master, $pdo);
            }
            throw $e;
        }
    }

    /**
     * 執行語句
     * @access public
     * @param  string        $sql sql指令
     * @param  array         $bind 参數绑定
     * @param  Query         $query 查詢對象
     * @return int
     * @throws PDOException
     * @throws \Exception
     */
    public function execute($sql, $bind = [], Query $query = null)
    {
        $this->initConnect(true);
        if (!$this->linkID) {
            return false;
        }

        // 记录SQL語句
        $this->queryStr = $sql;
        if ($bind) {
            $this->bind = $bind;
        }

        Db::$executeTimes++;
        try {
            // 调试開始
            $this->debug(true);

            // 预處理
            $this->PDOStatement = $this->linkID->prepare($sql);

            // 是否為存储过程调用
            $procedure = in_array(strtolower(substr(trim($sql), 0, 4)), ['call', 'exec']);
            // 参數绑定
            if ($procedure) {
                $this->bindParam($bind);
            } else {
                $this->bindValue($bind);
            }
            // 執行語句
            $this->PDOStatement->execute();
            // 调试结束
            $this->debug(false, '', true);

            if ($query && !empty($this->config['deploy']) && !empty($this->config['read_master'])) {
                $query->readMaster();
            }

            $this->numRows = $this->PDOStatement->rowCount();
            return $this->numRows;
        } catch (\PDOException $e) {
            if ($this->isBreak($e)) {
                return $this->close()->execute($sql, $bind, $query);
            }
            throw new PDOException($e, $this->config, $this->getLastsql());
        } catch (\Throwable $e) {
            if ($this->isBreak($e)) {
                return $this->close()->execute($sql, $bind, $query);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($this->isBreak($e)) {
                return $this->close()->execute($sql, $bind, $query);
            }
            throw $e;
        }
    }

    /**
     * 根據参數绑定组装最终的SQL語句 便于调试
     * @access public
     * @param string    $sql 带参數绑定的sql語句
     * @param array     $bind 参數绑定列表
     * @return string
     */
    public function getRealSql($sql, array $bind = [])
    {
        if (is_array($sql)) {
            $sql = implode(';', $sql);
        }

        foreach ($bind as $key => $val) {
            $value = is_array($val) ? $val[0] : $val;
            $type  = is_array($val) ? $val[1] : PDO::PARAM_STR;
            if (PDO::PARAM_STR == $type) {
                $value = $this->quote($value);
            } elseif (PDO::PARAM_INT == $type) {
                $value = (float) $value;
            }
            // 判断占位符
            $sql = is_numeric($key) ?
            substr_replace($sql, $value, strpos($sql, '?'), 1) :
            str_replace(
                [':' . $key . ')', ':' . $key . ',', ':' . $key . ' ', ':' . $key . PHP_EOL],
                [$value . ')', $value . ',', $value . ' ', $value . PHP_EOL],
                $sql . ' ');
        }
        return rtrim($sql);
    }

    /**
     * 参數绑定
     * 支援 ['name'=>'value','id'=>123] 對应命名占位符
     * 或者 ['value',123] 對应问号占位符
     * @access public
     * @param array $bind 要绑定的参數列表
     * @return void
     * @throws BindParamException
     */
    protected function bindValue(array $bind = [])
    {
        foreach ($bind as $key => $val) {
            // 占位符
            $param = is_numeric($key) ? $key + 1 : ':' . $key;
            if (is_array($val)) {
                if (PDO::PARAM_INT == $val[1] && '' === $val[0]) {
                    $val[0] = 0;
                }
                $result = $this->PDOStatement->bindValue($param, $val[0], $val[1]);
            } else {
                $result = $this->PDOStatement->bindValue($param, $val);
            }
            if (!$result) {
                throw new BindParamException(
                    "Error occurred  when binding parameters '{$param}'",
                    $this->config,
                    $this->getLastsql(),
                    $bind
                );
            }
        }
    }

    /**
     * 存储过程的输入输出参數绑定
     * @access public
     * @param array $bind 要绑定的参數列表
     * @return void
     * @throws BindParamException
     */
    protected function bindParam($bind)
    {
        foreach ($bind as $key => $val) {
            $param = is_numeric($key) ? $key + 1 : ':' . $key;
            if (is_array($val)) {
                array_unshift($val, $param);
                $result = call_user_func_array([$this->PDOStatement, 'bindParam'], $val);
            } else {
                $result = $this->PDOStatement->bindValue($param, $val);
            }
            if (!$result) {
                $param = array_shift($val);
                throw new BindParamException(
                    "Error occurred  when binding parameters '{$param}'",
                    $this->config,
                    $this->getLastsql(),
                    $bind
                );
            }
        }
    }

    /**
     * 获得資料集數组
     * @access protected
     * @param bool   $pdo 是否返回PDOStatement
     * @param bool   $procedure 是否存储过程
     * @return PDOStatement|array
     */
    protected function getResult($pdo = false, $procedure = false)
    {
        if ($pdo) {
            // 返回PDOStatement對象處理
            return $this->PDOStatement;
        }
        if ($procedure) {
            // 存储过程返回结果
            return $this->procedure();
        }
        $result        = $this->PDOStatement->fetchAll($this->fetchType);
        $this->numRows = count($result);
        return $result;
    }

    /**
     * 获得存储过程資料集
     * @access protected
     * @return array
     */
    protected function procedure()
    {
        $item = [];
        do {
            $result = $this->getResult();
            if ($result) {
                $item[] = $result;
            }
        } while ($this->PDOStatement->nextRowset());
        $this->numRows = count($item);
        return $item;
    }

    /**
     * 執行資料庫事务
     * @access public
     * @param callable $callback 資料操作方法回调
     * @return mixed
     * @throws PDOException
     * @throws \Exception
     * @throws \Throwable
     */
    public function transaction($callback)
    {
        $this->startTrans();
        try {
            $result = null;
            if (is_callable($callback)) {
                $result = call_user_func_array($callback, [$this]);
            }
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * 启動事务
     * @access public
     * @return bool|mixed
     * @throws \Exception
     */
    public function startTrans()
    {
        $this->initConnect(true);
        if (!$this->linkID) {
            return false;
        }

        ++$this->transTimes;
        try {
            if (1 == $this->transTimes) {
                $this->linkID->beginTransaction();
            } elseif ($this->transTimes > 1 && $this->supportSavepoint()) {
                $this->linkID->exec(
                    $this->parseSavepoint('trans' . $this->transTimes)
                );
            }

        } catch (\Exception $e) {
            if ($this->isBreak($e)) {
                --$this->transTimes;
                return $this->close()->startTrans();
            }
            throw $e;
        } catch (\Error $e) {
            if ($this->isBreak($e)) {
                --$this->transTimes;
                return $this->close()->startTrans();
            }
            throw $e;
        }
    }

    /**
     * 用于非自動送出狀態下面的查詢送出
     * @access public
     * @return void
     * @throws PDOException
     */
    public function commit()
    {
        $this->initConnect(true);

        if (1 == $this->transTimes) {
            $this->linkID->commit();
        }

        --$this->transTimes;
    }

    /**
     * 事务回滚
     * @access public
     * @return void
     * @throws PDOException
     */
    public function rollback()
    {
        $this->initConnect(true);

        if (1 == $this->transTimes) {
            $this->linkID->rollBack();
        } elseif ($this->transTimes > 1 && $this->supportSavepoint()) {
            $this->linkID->exec(
                $this->parseSavepointRollBack('trans' . $this->transTimes)
            );
        }

        $this->transTimes = max(0, $this->transTimes - 1);
    }

    /**
     * 是否支援事务嵌套
     * @return bool
     */
    protected function supportSavepoint()
    {
        return false;
    }

    /**
     * 產生定義保存點的SQL
     * @param $name
     * @return string
     */
    protected function parseSavepoint($name)
    {
        return 'SAVEPOINT ' . $name;
    }

    /**
     * 產生回滚到保存點的SQL
     * @param $name
     * @return string
     */
    protected function parseSavepointRollBack($name)
    {
        return 'ROLLBACK TO SAVEPOINT ' . $name;
    }

    /**
     * 批處理執行SQL語句
     * 批處理的指令都认為是execute操作
     * @access public
     * @param array $sqlArray SQL批處理指令
     * @return boolean
     */
    public function batchQuery($sqlArray = [], $bind = [], Query $query = null)
    {
        if (!is_array($sqlArray)) {
            return false;
        }
        // 自動启動事务支援
        $this->startTrans();
        try {
            foreach ($sqlArray as $sql) {
                $this->execute($sql, $bind, $query);
            }
            // 送出事务
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * 获得查詢次數
     * @access public
     * @param boolean $execute 是否包含所有查詢
     * @return integer
     */
    public function getQueryTimes($execute = false)
    {
        return $execute ? Db::$queryTimes + Db::$executeTimes : Db::$queryTimes;
    }

    /**
     * 获得執行次數
     * @access public
     * @return integer
     */
    public function getExecuteTimes()
    {
        return Db::$executeTimes;
    }

    /**
     * 關閉資料庫（或者重新連結）
     * @access public
     * @return $this
     */
    public function close()
    {
        $this->linkID    = null;
        $this->linkWrite = null;
        $this->linkRead  = null;
        $this->links     = [];
        // 释放查詢
        $this->free();
        return $this;
    }

    /**
     * 是否断线
     * @access protected
     * @param \PDOException|\Exception  $e 异常對象
     * @return bool
     */
    protected function isBreak($e)
    {
        if (!$this->config['break_reconnect']) {
            return false;
        }

        $info = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
            'failed with errno',
        ];

        $error = $e->getMessage();

        foreach ($info as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 取得最近一次查詢的sql語句
     * @access public
     * @return string
     */
    public function getLastSql()
    {
        return $this->getRealSql($this->queryStr, $this->bind);
    }

    /**
     * 取得最近插入的ID
     * @access public
     * @param string  $sequence     自增序列名
     * @return string
     */
    public function getLastInsID($sequence = null)
    {
        return $this->linkID->lastInsertId($sequence);
    }

    /**
     * 取得返回或者影响的记录數
     * @access public
     * @return integer
     */
    public function getNumRows()
    {
        return $this->numRows;
    }

    /**
     * 取得最近的錯誤訊息
     * @access public
     * @return string
     */
    public function getError()
    {
        if ($this->PDOStatement) {
            $error = $this->PDOStatement->errorInfo();
            $error = $error[1] . ':' . $error[2];
        } else {
            $error = '';
        }
        if ('' != $this->queryStr) {
            $error .= "\n [ SQL語句 ] : " . $this->getLastsql();
        }
        return $error;
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str SQL字符串
     * @param bool   $master 是否主庫查詢
     * @return string
     */
    public function quote($str, $master = true)
    {
        $this->initConnect($master);
        return $this->linkID ? $this->linkID->quote($str) : $str;
    }

    /**
     * 資料庫调试 记录當前SQL及分析性能
     * @access protected
     * @param boolean $start 调试開始标记 true 開始 false 结束
     * @param string  $sql 執行的SQL語句 留空自動取得
     * @param boolean $master 主从标记
     * @return void
     */
    protected function debug($start, $sql = '', $master = false)
    {
        if (!empty($this->config['debug'])) {
            // 開啟資料庫调试模式
            if ($start) {
                Debug::remark('queryStartTime', 'time');
            } else {
                // 记录操作结束時間
                Debug::remark('queryEndTime', 'time');
                $runtime = Debug::getRangeTime('queryStartTime', 'queryEndTime');
                $sql     = $sql ?: $this->getLastsql();
                $result  = [];
                // SQL性能分析
                if ($this->config['sql_explain'] && 0 === stripos(trim($sql), 'select')) {
                    $result = $this->getExplain($sql);
                }
                // SQL监听
                $this->trigger($sql, $runtime, $result, $master);
            }
        }
    }

    /**
     * 监听SQL執行
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
     * @param string    $sql SQL語句
     * @param float     $runtime SQL运行時間
     * @param mixed     $explain SQL分析
     * @param  bool     $master 主从标记
     * @return void
     */
    protected function trigger($sql, $runtime, $explain = [], $master = false)
    {
        if (!empty(self::$event)) {
            foreach (self::$event as $callback) {
                if (is_callable($callback)) {
                    call_user_func_array($callback, [$sql, $runtime, $explain, $master]);
                }
            }
        } else {
            // 未註冊监听則记录到日誌中
            if ($this->config['deploy']) {
                // 分布式记录當前操作的主从
                $master = $master ? 'master|' : 'slave|';
            } else {
                $master = '';
            }

            Log::record('[ SQL ] ' . $sql . ' [ ' . $master . 'RunTime:' . $runtime . 's ]', 'sql');
            if (!empty($explain)) {
                Log::record('[ EXPLAIN : ' . var_export($explain, true) . ' ]', 'sql');
            }
        }
    }

    /**
     * 初始化資料庫連結
     * @access protected
     * @param boolean $master 是否主服务器
     * @return void
     */
    protected function initConnect($master = true)
    {
        if (!empty($this->config['deploy'])) {
            // 采用分布式資料庫
            if ($master || $this->transTimes) {
                if (!$this->linkWrite) {
                    $this->linkWrite = $this->multiConnect(true);
                }
                $this->linkID = $this->linkWrite;
            } else {
                if (!$this->linkRead) {
                    $this->linkRead = $this->multiConnect(false);
                }
                $this->linkID = $this->linkRead;
            }
        } elseif (!$this->linkID) {
            // 默認單資料庫
            $this->linkID = $this->connect();
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
        // 分布式資料庫配置解析
        foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn', 'charset'] as $name) {
            $_config[$name] = explode(',', $this->config[$name]);
        }

        // 主服务器序号
        $m = floor(mt_rand(0, $this->config['master_num'] - 1));

        if ($this->config['rw_separate']) {
            // 主从式采用读寫分离
            if ($master) // 主服务器寫入
            {
                $r = $m;
            } elseif (is_numeric($this->config['slave_no'])) {
                // 指定服务器读
                $r = $this->config['slave_no'];
            } else {
                // 读操作連結从服务器 每次随機連結的資料庫
                $r = floor(mt_rand($this->config['master_num'], count($_config['hostname']) - 1));
            }
        } else {
            // 读寫操作不区分服务器 每次随機連結的資料庫
            $r = floor(mt_rand(0, count($_config['hostname']) - 1));
        }
        $dbMaster = false;
        if ($m != $r) {
            $dbMaster = [];
            foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn', 'charset'] as $name) {
                $dbMaster[$name] = isset($_config[$name][$m]) ? $_config[$name][$m] : $_config[$name][0];
            }
        }
        $dbConfig = [];
        foreach (['username', 'password', 'hostname', 'hostport', 'database', 'dsn', 'charset'] as $name) {
            $dbConfig[$name] = isset($_config[$name][$r]) ? $_config[$name][$r] : $_config[$name][0];
        }
        return $this->connect($dbConfig, $r, $r == $m ? false : $dbMaster);
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        // 释放查詢
        if ($this->PDOStatement) {
            $this->free();
        }
        // 關閉連結
        $this->close();
    }
}
