<?php

namespace traits\model;

use think\Collection;
use think\db\Query;
use think\Model;

/**
 * @mixin \Think\Model
 */
trait SoftDelete
{
    /**
     * 判断當前實例是否被软刪除
     * @access public
     * @return boolean
     */
    public function trashed()
    {
        $field = $this->getDeleteTimeField();

        if ($field && !empty($this->data[$field])) {
            return true;
        }
        return false;
    }

    /**
     * 查詢包含软刪除的資料
     * @access public
     * @return Query
     */
    public static function withTrashed()
    {
        return (new static )->getQuery();
    }

    /**
     * 只查詢软刪除資料
     * @access public
     * @return Query
     */
    public static function onlyTrashed()
    {
        $model = new static();
        $field = $model->getDeleteTimeField(true);

        if ($field) {
            return $model->getQuery()->useSoftDelete($field, ['not null', '']);
        } else {
            return $model->getQuery();
        }
    }

    /**
     * 刪除當前的记录
     * @access public
     * @param bool $force 是否强制刪除
     * @return integer
     */
    public function delete($force = false)
    {
        if (false === $this->trigger('before_delete', $this)) {
            return false;
        }

        $name = $this->getDeleteTimeField();
        if ($name && !$force) {
            // 软刪除
            $this->data[$name] = $this->autoWriteTimestamp($name);
            $result            = $this->isUpdate()->save();
        } else {
            // 强制刪除當前模型資料
            $result = $this->getQuery()->where($this->getWhere())->delete();
        }

        // 关联刪除
        if (!empty($this->relationWrite)) {
            foreach ($this->relationWrite as $key => $name) {
                $name   = is_numeric($key) ? $name : $key;
                $result = $this->getRelation($name);
                if ($result instanceof Model) {
                    $result->delete();
                } elseif ($result instanceof Collection || is_array($result)) {
                    foreach ($result as $model) {
                        $model->delete();
                    }
                }
            }
        }

        $this->trigger('after_delete', $this);

        // 清空原始資料
        $this->origin = [];

        return $result;
    }

    /**
     * 刪除记录
     * @access public
     * @param mixed $data  主键列表(支援闭包查詢條件)
     * @param bool  $force 是否强制刪除
     * @return integer 成功刪除的记录數
     */
    public static function destroy($data, $force = false)
    {
        if (is_null($data)) {
            return 0;
        }

        // 包含软刪除資料
        $query = (new static())->db(false);
        if (is_array($data) && key($data) !== 0) {
            $query->where($data);
            $data = null;
        } elseif ($data instanceof \Closure) {
            call_user_func_array($data, [ & $query]);
            $data = null;
        }

        $count = 0;
        if ($resultSet = $query->select($data)) {
            foreach ($resultSet as $data) {
                $result = $data->delete($force);
                $count += $result;
            }
        }

        return $count;
    }

    /**
     * 恢复被软刪除的记录
     * @access public
     * @param array $where 更新條件
     * @return integer
     */
    public function restore($where = [])
    {
        if (empty($where)) {
            $pk         = $this->getPk();
            $where[$pk] = $this->getData($pk);
        }

        $name = $this->getDeleteTimeField();

        if ($name) {
            // 恢复刪除
            return $this->getQuery()
                ->useSoftDelete($name, ['not null', ''])
                ->where($where)
                ->update([$name => null]);
        } else {
            return 0;
        }
    }

    /**
     * 查詢默認不包含软刪除資料
     * @access protected
     * @param Query $query 查詢對象
     * @return Query
     */
    protected function base($query)
    {
        $field = $this->getDeleteTimeField(true);
        return $field ? $query->useSoftDelete($field) : $query;
    }

    /**
     * 取得软刪除字段
     * @access public
     * @param bool $read 是否查詢操作(寫操作的时候会自動去掉表别名)
     * @return string
     */
    protected function getDeleteTimeField($read = false)
    {
        $field = property_exists($this, 'deleteTime') && isset($this->deleteTime) ?
        $this->deleteTime :
        'delete_time';

        if (false === $field) {
            return false;
        }

        if (!strpos($field, '.')) {
            $field = '__TABLE__.' . $field;
        }

        if (!$read && strpos($field, '.')) {
            $array = explode('.', $field);
            $field = array_pop($array);
        }

        return $field;
    }
}
