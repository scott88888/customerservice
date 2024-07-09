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
use think\Exception;
use think\Loader;
use think\Model;
use think\model\Relation;

class MorphOne extends Relation
{
    // 多态字段
    protected $morphKey;
    protected $morphType;
    // 多态类型
    protected $type;

    /**
     * 构造函數
     * @access public
     * @param Model  $parent    上级模型對象
     * @param string $model     模型名
     * @param string $morphKey  关联外键
     * @param string $morphType 多态字段名
     * @param string $type      多态类型
     */
    public function __construct(Model $parent, $model, $morphKey, $morphType, $type)
    {
        $this->parent    = $parent;
        $this->model     = $model;
        $this->type      = $type;
        $this->morphKey  = $morphKey;
        $this->morphType = $morphType;
        $this->query     = (new $model)->db();
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
        $relationModel = $this->relation($subRelation)->find();

        if ($relationModel) {
            $relationModel->setParent(clone $this->parent);
        }

        return $relationModel;
    }

    /**
     * 根據关联條件查詢当前模型
     * @access public
     * @param string  $operator 比较操作符
     * @param integer $count    个數
     * @param string  $id       关联表的统计字段
     * @param string  $joinType JOIN类型
     * @return Query
     */
    public function has($operator = '>=', $count = 1, $id = '*', $joinType = 'INNER')
    {
        return $this->parent;
    }

    /**
     * 根據关联條件查詢当前模型
     * @access public
     * @param  mixed $where  查詢條件（數组或者闭包）
     * @param  mixed $fields 字段
     * @return Query
     */
    public function hasWhere($where = [], $fields = null)
    {
        throw new Exception('relation not support: hasWhere');
    }

    /**
     * 预载入关联查詢
     * @access public
     * @param array    $resultSet   資料集
     * @param string   $relation    当前关联名
     * @param string   $subRelation 子关联名
     * @param \Closure $closure     闭包
     * @return void
     */
    public function eagerlyResultSet(&$resultSet, $relation, $subRelation, $closure)
    {
        $morphType = $this->morphType;
        $morphKey  = $this->morphKey;
        $type      = $this->type;
        $range     = [];
        foreach ($resultSet as $result) {
            $pk = $result->getPk();
            // 取得关联外键列表
            if (isset($result->$pk)) {
                $range[] = $result->$pk;
            }
        }

        if (!empty($range)) {
            $data = $this->eagerlyMorphToOne([
                $morphKey  => ['in', $range],
                $morphType => $type,
            ], $relation, $subRelation, $closure);
            // 关联属性名
            $attr = Loader::parseName($relation);
            // 关联資料封装
            foreach ($resultSet as $result) {
                if (!isset($data[$result->$pk])) {
                    $relationModel = null;
                } else {
                    $relationModel = $data[$result->$pk];
                    $relationModel->setParent(clone $result);
                    $relationModel->isUpdate(true);
                }

                $result->setRelation($attr, $relationModel);
            }
        }
    }

    /**
     * 预载入关联查詢
     * @access public
     * @param Model    $result      資料對象
     * @param string   $relation    当前关联名
     * @param string   $subRelation 子关联名
     * @param \Closure $closure     闭包
     * @return void
     */
    public function eagerlyResult(&$result, $relation, $subRelation, $closure)
    {
        $pk = $result->getPk();
        if (isset($result->$pk)) {
            $pk   = $result->$pk;
            $data = $this->eagerlyMorphToOne([
                $this->morphKey  => $pk,
                $this->morphType => $this->type,
            ], $relation, $subRelation, $closure);

            if (isset($data[$pk])) {
                $relationModel = $data[$pk];
                $relationModel->setParent(clone $result);
                $relationModel->isUpdate(true);
            } else {
                $relationModel = null;
            }

            $result->setRelation(Loader::parseName($relation), $relationModel);
        }
    }

    /**
     * 多态一對一 关联模型预查詢
     * @access   public
     * @param array         $where       关联预查詢條件
     * @param string        $relation    关联名
     * @param string        $subRelation 子关联
     * @param bool|\Closure $closure     闭包
     * @return array
     */
    protected function eagerlyMorphToOne($where, $relation, $subRelation = '', $closure = false)
    {
        // 预载入关联查詢 支持嵌套预载入
        if ($closure) {
            call_user_func_array($closure, [ & $this]);
        }
        $list     = $this->query->where($where)->with($subRelation)->find();
        $morphKey = $this->morphKey;
        // 组装模型資料
        $data = [];
        foreach ($list as $set) {
            $data[$set->$morphKey][] = $set;
        }
        return $data;
    }

    /**
     * 保存（新增）当前关联資料對象
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
        $pk = $this->parent->getPk();

        $data[$this->morphKey]  = $this->parent->$pk;
        $data[$this->morphType] = $this->type;

        $model = new $this->model();

        return $model->save() ? $model : false;
    }

    /**
     * 建立关联對象实例
     * @param array $data
     * @return Model
     */
    public function make($data = [])
    {
        if ($data instanceof Model) {
            $data = $data->getData();
        }
        // 保存关联表資料
        $pk = $this->parent->getPk();

        $data[$this->morphKey]  = $this->parent->$pk;
        $data[$this->morphType] = $this->type;

        return new $this->model($data);
    }

    /**
     * 执行基础查詢（进执行一次）
     * @access protected
     * @return void
     */
    protected function baseQuery()
    {
        if (empty($this->baseQuery) && $this->parent->getData()) {
            $pk                    = $this->parent->getPk();
            $map[$this->morphKey]  = $this->parent->$pk;
            $map[$this->morphType] = $this->type;
            $this->query->where($map);
            $this->baseQuery = true;
        }
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
        throw new Exception('relation not support: withCount');
    }
}
