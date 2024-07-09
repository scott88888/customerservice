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

use think\Collection;
use think\Db;
use think\db\Query;
use think\Exception;
use think\Loader;
use think\Model;
use think\model\Pivot;
use think\model\Relation;
use think\Paginator;

class BelongsToMany extends Relation
{
    // 中间表表名
    protected $middle;
    // 中间表模型名稱
    protected $pivotName;
    // 中间表模型對象
    protected $pivot;
    // 中间表資料名稱
    protected $pivotDataName = 'pivot';

    /**
     * 构造函數
     * @access public
     * @param Model  $parent     上级模型對象
     * @param string $model      模型名
     * @param string $table      中间表名
     * @param string $foreignKey 关联模型外键
     * @param string $localKey   當前模型关联键
     */
    public function __construct(Model $parent, $model, $table, $foreignKey, $localKey)
    {
        $this->parent     = $parent;
        $this->model      = $model;
        $this->foreignKey = $foreignKey;
        $this->localKey   = $localKey;
        if (false !== strpos($table, '\\')) {
            $this->pivotName = $table;
            $this->middle    = basename(str_replace('\\', '/', $table));
        } else {
            $this->middle = $table;
        }
        $this->query = (new $model)->db();
        $this->pivot = $this->newPivot();

        if ('think\model\Pivot' == get_class($this->pivot)) {
            $this->pivot->name($this->middle);
        }
    }

    /**
     * 設定中间表模型
     * @param $pivot
     * @return $this
     */
    public function pivot($pivot)
    {
        $this->pivotName = $pivot;
        return $this;
    }

    /**
     * 設定中间表資料名稱
     * @access public
     * @param  string $name
     * @return $this
     */
    public function pivotDataName($name)
    {
        $this->pivotDataName = $name;
        return $this;
    }

    /**
     * 取得中间表更新條件
     * @param $data
     * @return array
     */
    protected function getUpdateWhere($data)
    {
        return [
            $this->localKey   => $data[$this->localKey],
            $this->foreignKey => $data[$this->foreignKey],
        ];
    }

    /**
     * 實例化中间表模型
     * @param  array    $data
     * @param  bool     $isUpdate
     * @return Pivot
     * @throws Exception
     */
    protected function newPivot($data = [], $isUpdate = false)
    {
        $class = $this->pivotName ?: '\\think\\model\\Pivot';
        $pivot = new $class($data, $this->parent, $this->middle);
        if ($pivot instanceof Pivot) {
            return $isUpdate ? $pivot->isUpdate(true, $this->getUpdateWhere($data)) : $pivot;
        } else {
            throw new Exception('pivot model must extends: \think\model\Pivot');
        }
    }

    /**
     * 合成中间表模型
     * @param array|Collection|Paginator $models
     */
    protected function hydratePivot($models)
    {
        foreach ($models as $model) {
            $pivot = [];
            foreach ($model->getData() as $key => $val) {
                if (strpos($key, '__')) {
                    list($name, $attr) = explode('__', $key, 2);
                    if ('pivot' == $name) {
                        $pivot[$attr] = $val;
                        unset($model->$key);
                    }
                }
            }
            $model->setRelation($this->pivotDataName, $this->newPivot($pivot, true));
        }
    }

    /**
     * 建立关联查詢Query對象
     * @return Query
     */
    protected function buildQuery()
    {
        $foreignKey = $this->foreignKey;
        $localKey   = $this->localKey;
        $pk         = $this->parent->getPk();
        // 关联查詢
        $condition['pivot.' . $localKey] = $this->parent->$pk;
        return $this->belongsToManyQuery($foreignKey, $localKey, $condition);
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
        $result = $this->buildQuery()->relation($subRelation)->select();
        $this->hydratePivot($result);
        return $result;
    }

    /**
     * 重载select方法
     * @param null $data
     * @return false|\PDOStatement|string|Collection
     */
    public function select($data = null)
    {
        $result = $this->buildQuery()->select($data);
        $this->hydratePivot($result);
        return $result;
    }

    /**
     * 重载paginate方法
     * @param null  $listRows
     * @param bool  $simple
     * @param array $config
     * @return Paginator
     */
    public function paginate($listRows = null, $simple = false, $config = [])
    {
        $result = $this->buildQuery()->paginate($listRows, $simple, $config);
        $this->hydratePivot($result);
        return $result;
    }

    /**
     * 重载find方法
     * @param null $data
     * @return array|false|\PDOStatement|string|Model
     */
    public function find($data = null)
    {
        $result = $this->buildQuery()->find($data);
        if ($result) {
            $this->hydratePivot([$result]);
        }
        return $result;
    }

    /**
     * 查找多條记录 如果不存在則抛出异常
     * @access public
     * @param array|string|Query|\Closure $data
     * @return array|\PDOStatement|string|Model
     */
    public function selectOrFail($data = null)
    {
        return $this->failException(true)->select($data);
    }

    /**
     * 查找單條记录 如果不存在則抛出异常
     * @access public
     * @param array|string|Query|\Closure $data
     * @return array|\PDOStatement|string|Model
     */
    public function findOrFail($data = null)
    {
        return $this->failException(true)->find($data);
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
        return $this->parent;
    }

    /**
     * 根據关联條件查詢當前模型
     * @access public
     * @param  mixed  $where 查詢條件（數组或者闭包）
     * @param  mixed  $fields   字段
     * @return Query
     * @throws Exception
     */
    public function hasWhere($where = [], $fields = null)
    {
        throw new Exception('relation not support: hasWhere');
    }

    /**
     * 設定中间表的查詢條件
     * @param      $field
     * @param null $op
     * @param null $condition
     * @return $this
     */
    public function wherePivot($field, $op = null, $condition = null)
    {
        $field = 'pivot.' . $field;
        $this->query->where($field, $op, $condition);
        return $this;
    }

    /**
     * 预载入关联查詢（資料集）
     * @access public
     * @param array    $resultSet   資料集
     * @param string   $relation    當前关联名
     * @param string   $subRelation 子关联名
     * @param \Closure $closure     闭包
     * @return void
     */
    public function eagerlyResultSet(&$resultSet, $relation, $subRelation, $closure)
    {
        $localKey   = $this->localKey;
        $foreignKey = $this->foreignKey;

        $pk    = $resultSet[0]->getPk();
        $range = [];
        foreach ($resultSet as $result) {
            // 取得关联外键列表
            if (isset($result->$pk)) {
                $range[] = $result->$pk;
            }
        }

        if (!empty($range)) {
            // 查詢关联資料
            $data = $this->eagerlyManyToMany([
                'pivot.' . $localKey => [
                    'in',
                    $range,
                ],
            ], $relation, $subRelation);
            // 关联属性名
            $attr = Loader::parseName($relation);
            // 关联資料封装
            foreach ($resultSet as $result) {
                if (!isset($data[$result->$pk])) {
                    $data[$result->$pk] = [];
                }

                $result->setRelation($attr, $this->resultSetBuild($data[$result->$pk]));
            }
        }
    }

    /**
     * 预载入关联查詢（單个資料）
     * @access public
     * @param Model    $result      資料對象
     * @param string   $relation    當前关联名
     * @param string   $subRelation 子关联名
     * @param \Closure $closure     闭包
     * @return void
     */
    public function eagerlyResult(&$result, $relation, $subRelation, $closure)
    {
        $pk = $result->getPk();
        if (isset($result->$pk)) {
            $pk = $result->$pk;
            // 查詢管理資料
            $data = $this->eagerlyManyToMany(['pivot.' . $this->localKey => $pk], $relation, $subRelation);

            // 关联資料封装
            if (!isset($data[$pk])) {
                $data[$pk] = [];
            }
            $result->setRelation(Loader::parseName($relation), $this->resultSetBuild($data[$pk]));
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
        $pk    = $result->getPk();
        $count = 0;
        if (isset($result->$pk)) {
            $pk    = $result->$pk;
            $count = $this->belongsToManyQuery($this->foreignKey, $this->localKey, ['pivot.' . $this->localKey => $pk])->count();
        }
        return $count;
    }

    /**
     * 取得关联统计子查詢
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

        return $this->belongsToManyQuery($this->foreignKey, $this->localKey, [
            'pivot.' . $this->localKey => [
                'exp',
                Db::raw('=' . $this->parent->getTable() . '.' . $this->parent->getPk()),
            ],
        ])->fetchSql()->count();
    }

    /**
     * 多對多 关联模型预查詢
     * @access public
     * @param array  $where       关联预查詢條件
     * @param string $relation    关联名
     * @param string $subRelation 子关联
     * @return array
     */
    protected function eagerlyManyToMany($where, $relation, $subRelation = '')
    {
        // 预载入关联查詢 支援嵌套预载入
        $list = $this->belongsToManyQuery($this->foreignKey, $this->localKey, $where)->with($subRelation)->select();

        // 组装模型資料
        $data = [];
        foreach ($list as $set) {
            $pivot = [];
            foreach ($set->getData() as $key => $val) {
                if (strpos($key, '__')) {
                    list($name, $attr) = explode('__', $key, 2);
                    if ('pivot' == $name) {
                        $pivot[$attr] = $val;
                        unset($set->$key);
                    }
                }
            }
            $set->setRelation($this->pivotDataName, $this->newPivot($pivot, true));
            $data[$pivot[$this->localKey]][] = $set;
        }
        return $data;
    }

    /**
     * BELONGS TO MANY 关联查詢
     * @access public
     * @param string $foreignKey 关联模型关联键
     * @param string $localKey   當前模型关联键
     * @param array  $condition  关联查詢條件
     * @return Query
     */
    protected function belongsToManyQuery($foreignKey, $localKey, $condition = [])
    {
        // 关联查詢封装
        $tableName = $this->query->getTable();
        $table     = $this->pivot->getTable();
        $fields    = $this->getQueryFields($tableName);

        $query = $this->query->field($fields)
            ->field(true, false, $table, 'pivot', 'pivot__');

        if (empty($this->baseQuery)) {
            $relationFk = $this->query->getPk();
            $query->join([$table => 'pivot'], 'pivot.' . $foreignKey . '=' . $tableName . '.' . $relationFk)
                ->where($condition);
        }
        return $query;
    }

    /**
     * 保存（新增）當前关联資料對象
     * @access public
     * @param mixed $data  資料 可以使用數组 关联模型對象 和 关联對象的主键
     * @param array $pivot 中间表額外資料
     * @return integer
     */
    public function save($data, array $pivot = [])
    {
        // 保存关联表/中间表資料
        return $this->attach($data, $pivot);
    }

    /**
     * 批量保存當前关联資料對象
     * @access public
     * @param array $dataSet   資料集
     * @param array $pivot     中间表額外資料
     * @param bool  $samePivot 額外資料是否相同
     * @return integer
     */
    public function saveAll(array $dataSet, array $pivot = [], $samePivot = false)
    {
        $result = false;
        foreach ($dataSet as $key => $data) {
            if (!$samePivot) {
                $pivotData = isset($pivot[$key]) ? $pivot[$key] : [];
            } else {
                $pivotData = $pivot;
            }
            $result = $this->attach($data, $pivotData);
        }
        return $result;
    }

    /**
     * 附加关联的一个中间表資料
     * @access public
     * @param mixed $data  資料 可以使用數组、关联模型對象 或者 关联對象的主键
     * @param array $pivot 中间表額外資料
     * @return array|Pivot
     * @throws Exception
     */
    public function attach($data, $pivot = [])
    {
        if (is_array($data)) {
            if (key($data) === 0) {
                $id = $data;
            } else {
                // 保存关联表資料
                $model = new $this->model;
                $model->save($data);
                $id = $model->getLastInsID();
            }
        } elseif (is_numeric($data) || is_string($data)) {
            // 根據关联表主键直接寫入中间表
            $id = $data;
        } elseif ($data instanceof Model) {
            // 根據关联表主键直接寫入中间表
            $relationFk = $data->getPk();
            $id         = $data->$relationFk;
        }

        if ($id) {
            // 保存中间表資料
            $pk                     = $this->parent->getPk();
            $pivot[$this->localKey] = $this->parent->$pk;
            $ids                    = (array) $id;
            foreach ($ids as $id) {
                $pivot[$this->foreignKey] = $id;
                $this->pivot->insert($pivot, true);
                $result[] = $this->newPivot($pivot, true);
            }
            if (count($result) == 1) {
                // 返回中间表模型對象
                $result = $result[0];
            }
            return $result;
        } else {
            throw new Exception('miss relation data');
        }
    }

    /**
     * 判断是否存在关联資料
     * @access public
     * @param  mixed $data  資料 可以使用关联模型對象 或者 关联對象的主键
     * @return Pivot
     * @throws Exception
     */
    public function attached($data)
    {
        if ($data instanceof Model) {
            $relationFk = $data->getPk();
            $id         = $data->$relationFk;
        } else {
            $id = $data;
        }

        $pk = $this->parent->getPk();

        $pivot = $this->pivot->where($this->localKey, $this->parent->$pk)->where($this->foreignKey, $id)->find();

        return $pivot ?: false;
    }

    /**
     * 解除关联的一个中间表資料
     * @access public
     * @param integer|array $data        資料 可以使用关联對象的主键
     * @param bool          $relationDel 是否同时刪除关联表資料
     * @return integer
     */
    public function detach($data = null, $relationDel = false)
    {
        if (is_array($data)) {
            $id = $data;
        } elseif (is_numeric($data) || is_string($data)) {
            // 根據关联表主键直接寫入中间表
            $id = $data;
        } elseif ($data instanceof Model) {
            // 根據关联表主键直接寫入中间表
            $relationFk = $data->getPk();
            $id         = $data->$relationFk;
        }
        // 刪除中间表資料
        $pk                     = $this->parent->getPk();
        $pivot[$this->localKey] = $this->parent->$pk;
        if (isset($id)) {
            $pivot[$this->foreignKey] = is_array($id) ? ['in', $id] : $id;
        }
        $this->pivot->where($pivot)->delete();
        // 刪除关联表資料
        if (isset($id) && $relationDel) {
            $model = $this->model;
            $model::destroy($id);
        }
    }

    /**
     * 資料同步
     * @param array $ids
     * @param bool  $detaching
     * @return array
     */
    public function sync($ids, $detaching = true)
    {
        $changes = [
            'attached' => [],
            'detached' => [],
            'updated'  => [],
        ];
        $pk      = $this->parent->getPk();
        $current = $this->pivot->where($this->localKey, $this->parent->$pk)
            ->column($this->foreignKey);
        $records = [];

        foreach ($ids as $key => $value) {
            if (!is_array($value)) {
                $records[$value] = [];
            } else {
                $records[$key] = $value;
            }
        }

        $detach = array_diff($current, array_keys($records));

        if ($detaching && count($detach) > 0) {
            $this->detach($detach);

            $changes['detached'] = $detach;
        }

        foreach ($records as $id => $attributes) {
            if (!in_array($id, $current)) {
                $this->attach($id, $attributes);
                $changes['attached'][] = $id;
            } elseif (count($attributes) > 0 &&
                $this->attach($id, $attributes)
            ) {
                $changes['updated'][] = $id;
            }
        }

        return $changes;

    }

    /**
     * 執行基础查詢（进執行一次）
     * @access protected
     * @return void
     */
    protected function baseQuery()
    {
        if (empty($this->baseQuery) && $this->parent->getData()) {
            $pk    = $this->parent->getPk();
            $table = $this->pivot->getTable();
            $this->query->join([$table => 'pivot'], 'pivot.' . $this->foreignKey . '=' . $this->query->getTable() . '.' . $this->query->getPk())->where('pivot.' . $this->localKey, $this->parent->$pk);
            $this->baseQuery = true;
        }
    }

}
