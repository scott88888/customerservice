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

namespace think\model;

use think\Db;
use think\db\Query;
use think\Model;

class Merge extends Model
{

    protected $relationModel = []; // HAS ONE 关联的模型列表
    protected $fk            = ''; //  外键名 默认为主表名_id
    protected $mapFields     = []; //  需要处理的模型映射字段，避免混淆 array( id => 'user.id'  )

    /**
     * 构造函数
     * @access public
     * @param array|object $data 資料
     */
    public function __construct($data = [])
    {
        parent::__construct($data);

        // 设置默认外键名 仅支持单一外键
        if (empty($this->fk)) {
            $this->fk = strtolower($this->name) . '_id';
        }
    }

    /**
     * 查找单條记录
     * @access public
     * @param mixed        $data  主键值或者查詢條件（闭包）
     * @param string|array $with  关联预查詢
     * @param bool         $cache 是否缓存
     * @return \think\Model
     */
    public static function get($data = null, $with = [], $cache = false)
    {
        $query = self::parseQuery($data, $with, $cache);
        $query = self::attachQuery($query);
        return $query->find($data);
    }

    /**
     * 附加查詢表达式
     * @access protected
     * @param \think\db\Query $query 查詢对象
     * @return \think\db\Query
     */
    protected static function attachQuery($query)
    {
        $class  = new static();
        $master = $class->name;
        $fields = self::getModelField($query, $master, '', $class->mapFields, $class->field);
        $query->alias($master)->field($fields);

        foreach ($class->relationModel as $key => $model) {
            $name  = is_int($key) ? $model : $key;
            $table = is_int($key) ? $query->getTable($name) : $model;
            $query->join($table . ' ' . $name, $name . '.' . $class->fk . '=' . $master . '.' . $class->getPk());
            $fields = self::getModelField($query, $name, $table, $class->mapFields, $class->field);
            $query->field($fields);
        }
        return $query;
    }

    /**
     * 取得关联模型的字段 并解决混淆
     * @access protected
     * @param \think\db\Query $query  查詢对象
     * @param string          $name   模型名稱
     * @param string          $table  关联表名稱
     * @param array           $map    字段映射
     * @param array           $fields 查詢字段
     * @return array
     */
    protected static function getModelField($query, $name, $table = '', $map = [], $fields = [])
    {
        // 取得模型的字段訊息
        $fields = $fields ?: $query->getTableInfo($table, 'fields');
        $array  = [];
        foreach ($fields as $field) {
            if ($key = array_search($name . '.' . $field, $map)) {
                // 需要处理映射字段
                $array[] = $name . '.' . $field . ' AS ' . $key;
            } else {
                $array[] = $field;
            }
        }
        return $array;
    }

    /**
     * 查找所有记录
     * @access public
     * @param mixed        $data 主键列表或者查詢條件（闭包）
     * @param array|string $with 关联预查詢
     * @param bool         $cache
     * @return array|false|string
     */
    public static function all($data = null, $with = [], $cache = false)
    {
        $query = self::parseQuery($data, $with, $cache);
        $query = self::attachQuery($query);
        return $query->select($data);
    }

    /**
     * 处理写入的模型資料
     * @access public
     * @param string $model  模型名稱
     * @param array  $data   資料
     * @return array
     */
    protected function parseData($model, $data)
    {
        $item = [];
        foreach ($data as $key => $val) {
            if ($this->fk != $key && array_key_exists($key, $this->mapFields)) {
                list($name, $key) = explode('.', $this->mapFields[$key]);
                if ($model == $name) {
                    $item[$key] = $val;
                }
            } else {
                $item[$key] = $val;
            }
        }
        return $item;
    }

    /**
     * 保存模型資料 以及关联資料
     * @access public
     * @param mixed  $data     資料
     * @param array  $where    更新條件
     * @param string $sequence 自增序列名
     * @return false|int
     * @throws \Exception
     */
    public function save($data = [], $where = [], $sequence = null)
    {
        if (!empty($data)) {
            // 資料自动驗證
            if (!$this->validateData($data)) {
                return false;
            }
            // 資料对象赋值
            foreach ($data as $key => $value) {
                $this->setAttr($key, $value, $data);
            }
            if (!empty($where)) {
                $this->isUpdate = true;
            }
        }

        // 資料自动完成
        $this->autoCompleteData($this->auto);

        // 自动写入更新时间
        if ($this->autoWriteTimestamp && $this->updateTime && !isset($this->data[$this->updateTime])) {
            $this->setAttr($this->updateTime, null);
        }

        // 事件回调
        if (false === $this->trigger('before_write', $this)) {
            return false;
        }

        $db = $this->db();
        $db->startTrans();
        $pk = $this->getPk();
        try {
            if ($this->isUpdate) {
                // 自动写入
                $this->autoCompleteData($this->update);

                if (false === $this->trigger('before_update', $this)) {
                    return false;
                }

                if (empty($where) && !empty($this->updateWhere)) {
                    $where = $this->updateWhere;
                }

                // 取得有更新的資料
                $data = $this->getChangedData();
                // 保留主键資料
                foreach ($this->data as $key => $val) {
                    if ($this->isPk($key)) {
                        $data[$key] = $val;
                    }
                }
                // 处理模型資料
                $data = $this->parseData($this->name, $data);
                if (is_string($pk) && isset($data[$pk])) {
                    if (!isset($where[$pk])) {
                        unset($where);
                        $where[$pk] = $data[$pk];
                    }
                    unset($data[$pk]);
                }
                // 写入主表資料
                $result = $db->strict(false)->where($where)->update($data);

                // 写入附表資料
                foreach ($this->relationModel as $key => $model) {
                    $name  = is_int($key) ? $model : $key;
                    $table = is_int($key) ? $db->getTable($model) : $model;
                    // 处理关联模型資料
                    $data = $this->parseData($name, $data);
                    if (Db::table($table)->strict(false)->where($this->fk, $this->data[$this->getPk()])->update($data)) {
                        $result = 1;
                    }
                }

                // 新增回调
                $this->trigger('after_update', $this);
            } else {
                // 自动写入
                $this->autoCompleteData($this->insert);

                // 自动写入创建时间
                if ($this->autoWriteTimestamp && $this->createTime && !isset($this->data[$this->createTime])) {
                    $this->setAttr($this->createTime, null);
                }

                if (false === $this->trigger('before_insert', $this)) {
                    return false;
                }

                // 处理模型資料
                $data = $this->parseData($this->name, $this->data);
                // 写入主表資料
                $result = $db->name($this->name)->strict(false)->insert($data);
                if ($result) {
                    $insertId = $db->getLastInsID($sequence);
                    // 写入外键資料
                    if ($insertId) {
                        if (is_string($pk)) {
                            $this->data[$pk] = $insertId;
                        }
                        $this->data[$this->fk] = $insertId;
                    }

                    // 写入附表資料
                    $source = $this->data;
                    if ($insertId && is_string($pk) && isset($source[$pk]) && $this->fk != $pk) {
                        unset($source[$pk]);
                    }
                    foreach ($this->relationModel as $key => $model) {
                        $name  = is_int($key) ? $model : $key;
                        $table = is_int($key) ? $db->getTable($model) : $model;
                        // 处理关联模型資料
                        $data = $this->parseData($name, $source);
                        Db::table($table)->strict(false)->insert($data);
                    }
                }
                // 标记为更新
                $this->isUpdate = true;
                // 新增回调
                $this->trigger('after_insert', $this);
            }
            $db->commit();
            // 写入回调
            $this->trigger('after_write', $this);

            $this->origin = $this->data;
            return $result;
        } catch (\Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /**
     * 刪除当前的记录 并刪除关联資料
     * @access public
     * @return int
     * @throws \Exception
     */
    public function delete()
    {
        if (false === $this->trigger('before_delete', $this)) {
            return false;
        }

        $db = $this->db();
        $db->startTrans();
        try {
            $result = $db->delete($this->data);
            if ($result) {
                // 取得主键資料
                $pk = $this->data[$this->getPk()];

                // 刪除关联資料
                foreach ($this->relationModel as $key => $model) {
                    $table = is_int($key) ? $db->getTable($model) : $model;
                    $query = new Query;
                    $query->table($table)->where($this->fk, $pk)->delete();
                }
            }
            $this->trigger('after_delete', $this);
            $db->commit();
            return $result;
        } catch (\Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

}
