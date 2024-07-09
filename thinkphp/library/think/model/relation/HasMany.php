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

namespace think\model\relation;

use think\db\Query;
use think\Loader;
use think\Model;
use think\model\Relation;

class HasMany extends Relation
{
    /**
     * 构造函數
     * @access public
     * @param Model  $parent     上级模型對象
     * @param string $model      模型名
     * @param string $foreignKey 关联外键
     * @param string $localKey   當前模型主键
     */
    public function __construct(Model $parent, $model, $foreignKey, $localKey)
    {
        $this->parent     = $parent;
        $this->model      = $model;
        $this->foreignKey = $foreignKey;
        $this->localKey   = $localKey;
        $this->query      = (new $model)->db();
    }

    /**
     * 延迟取得关联資料
     * @param string   $subRelation 子关联名
     * @param \Closure $closure     闭包查詢條件
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRelation($subRelation = '', $closure = null)
    {
        if ($closure) {
            call_user_func_array($closure, [ & $this->query]);
        }
        $list   = $this->relation($subRelation)->select();
        $parent = clone $this->parent;

        foreach ($list as &$model) {
            $model->setParent($parent);
        }

        return $list;
    }

    /**
     * 预载入关联查詢
     * @access   public
     * @param array    $resultSet   資料集
     * @param string   $relation    當前关联名
     * @param string   $subRelation 子关联名
     * @param \Closure $closure     闭包
     * @return void
     */
    public function eagerlyResultSet(&$resultSet, $relation, $subRelation, $closure)
    {
        $localKey = $this->localKey;
        $range    = [];
        foreach ($resultSet as $result) {
            // 取得关联外键列表
            if (isset($result->$localKey)) {
                $range[] = $result->$localKey;
            }
        }

        if (!empty($range)) {
            $data = $this->eagerlyOneToMany($this->query, [
                $this->foreignKey => [
                    'in',
                    $range,
                ],
            ], $relation, $subRelation, $closure);
            // 关联属性名
            $attr = Loader::parseName($relation);
            // 关联資料封装
            foreach ($resultSet as $result) {
                if (!isset($data[$result->$localKey])) {
                    $data[$result->$localKey] = [];
                }

                foreach ($data[$result->$localKey] as &$relationModel) {
                    $relationModel->setParent(clone $result);
                }

                $result->setRelation($attr, $this->resultSetBuild($data[$result->$localKey]));
            }
        }
    }

    /**
     * 预载入关联查詢
     * @access   public
     * @param Model    $result      資料對象
     * @param string   $relation    當前关联名
     * @param string   $subRelation 子关联名
     * @param \Closure $closure     闭包
     * @return void
     */
    public function eagerlyResult(&$result, $relation, $subRelation, $closure)
    {
        $localKey = $this->localKey;

        if (isset($result->$localKey)) {
            $data = $this->eagerlyOneToMany($this->query, [$this->foreignKey => $result->$localKey], $relation, $subRelation, $closure);
            // 关联資料封装
            if (!isset($data[$result->$localKey])) {
                $data[$result->$localKey] = [];
            }

            foreach ($data[$result->$localKey] as &$relationModel) {
                $relationModel->setParent(clone $result);
            }

            $result->setRelation(Loader::parseName($relation), $this->resultSetBuild($data[$result->$localKey]));
        }
    }

    /**
     * 关联统计
     * @access public
     * @param Model    $result  資料對象
     * @param \Closure $closure 闭包
     * @return integer
     */
    public function relationCount($result, $closure)
    {
        $localKey = $this->localKey;
        $count    = 0;
        if (isset($result->$localKey)) {
            if ($closure) {
                call_user_func_array($closure, [ & $this->query]);
            }
            $count = $this->query->where($this->foreignKey, $result->$localKey)->count();
        }
        return $count;
    }

    /**
     * 建立关联统计子查詢
     * @access public
     * @param \Closure $closure 闭包
     * @param string   $name    统计資料别名
     * @return string
     */
    public function getRelationCountQuery($closure, &$name = null)
    {
        if ($closure) {
            $return = call_user_func_array($closure, [ & $this->query]);
            if ($return && is_string($return)) {
                $name = $return;
            }
        }
        $localKey = $this->localKey ?: $this->parent->getPk();
        return $this->query->whereExp($this->foreignKey, '=' . $this->parent->getTable() . '.' . $localKey)->fetchSql()->count();
    }

    /**
     * 一對多 关联模型预查詢
     * @access public
     * @param object $model       关联模型對象
     * @param array  $where       关联预查詢條件
     * @param string $relation    关联名
     * @param string $subRelation 子关联
     * @param bool   $closure
     * @return array
     */
    protected function eagerlyOneToMany($model, $where, $relation, $subRelation = '', $closure = false)
    {
        $foreignKey = $this->foreignKey;
        // 预载入关联查詢 支援嵌套预载入
        if ($closure) {
            call_user_func_array($closure, [ & $model]);
        }
        $list = $model->removeWhereField($foreignKey)->where($where)->with($subRelation)->select();

        // 组装模型資料
        $data = [];
        foreach ($list as $set) {
            $data[$set->$foreignKey][] = $set;
        }
        return $data;
    }

    /**
     * 保存（新增）當前关联資料對象
     * @access public
     * @param mixed $data 資料 可以使用數组 关联模型對象 和 关联對象的主键
     * @return Model|false
     */
    public function save($data)
    {
        if ($data instanceof Model) {
            $data = $data->getData();
        }

        // 保存关联表資料
        $data[$this->foreignKey] = $this->parent->{$this->localKey};

        $model = new $this->model();
        return $model->save($data) ? $model : false;
    }

    /**
     * 建立关联對象實例
     * @param array $data
     * @return Model
     */
    public function make($data = [])
    {
        if ($data instanceof Model) {
            $data = $data->getData();
        }

        // 保存关联表資料
        $data[$this->foreignKey] = $this->parent->{$this->localKey};

        return new $this->model($data);
    }

    /**
     * 批量保存當前关联資料對象
     * @access public
     * @param array $dataSet 資料集
     * @return integer
     */
    public function saveAll(array $dataSet)
    {
        $result = false;
        foreach ($dataSet as $key => $data) {
            $result = $this->save($data);
        }
        return $result;
    }

    /**
     * 根據关联條件查詢當前模型
     * @access public
     * @param string  $operator 比较操作符
     * @param integer $count    个數
     * @param string  $id       关联表的统计字段
     * @param string  $joinType JOIN類型
     * @return Query
     */
    public function has($operator = '>=', $count = 1, $id = '*', $joinType = 'INNER')
    {
        $table    = $this->query->getTable();
        $model    = basename(str_replace('\\', '/', get_class($this->parent)));
        $relation = basename(str_replace('\\', '/', $this->model));

        return $this->parent->db()
            ->alias($model)
            ->field($model . '.*')
            ->join([$table => $relation], $model . '.' . $this->localKey . '=' . $relation . '.' . $this->foreignKey, $joinType)
            ->group($relation . '.' . $this->foreignKey)
            ->having('count(' . $id . ')' . $operator . $count);
    }

    /**
     * 根據关联條件查詢當前模型
     * @access public
     * @param  mixed     $where  查詢條件（數组或者闭包）
     * @param  mixed     $fields 字段
     * @return Query
     */
    public function hasWhere($where = [], $fields = null)
    {
        $table    = $this->query->getTable();
        $model    = basename(str_replace('\\', '/', get_class($this->parent)));
        $relation = basename(str_replace('\\', '/', $this->model));

        if (is_array($where)) {
            foreach ($where as $key => $val) {
                if (false === strpos($key, '.')) {
                    $where[$relation . '.' . $key] = $val;
                    unset($where[$key]);
                }
            }
        }

        $fields = $this->getRelationQueryFields($fields, $model);

        return $this->parent->db()->alias($model)
            ->field($fields)
            ->group($model . '.' . $this->localKey)
            ->join([$table => $relation], $model . '.' . $this->localKey . '=' . $relation . '.' . $this->foreignKey)
            ->where($where);
    }

    /**
     * 執行基础查詢（进執行一次）
     * @access protected
     * @return void
     */
    protected function baseQuery()
    {
        if (empty($this->baseQuery)) {
            if (isset($this->parent->{$this->localKey})) {
                // 关联查詢带入关联條件
                $this->query->where($this->foreignKey, $this->parent->{$this->localKey});
            }
            $this->baseQuery = true;
        }
    }

}
