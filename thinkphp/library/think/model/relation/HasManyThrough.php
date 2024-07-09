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

class HasManyThrough extends Relation
{
    // 中间关联表外键
    protected $throughKey;
    // 中间表模型
    protected $through;

    /**
     * 构造函數
     * @access   public
     * @param Model  $parent     上级模型對象
     * @param string $model      模型名
     * @param string $through    中间模型名
     * @param string $foreignKey 关联外键
     * @param string $throughKey 关联外键
     * @param string $localKey   关联主键
     */
    public function __construct(Model $parent, $model, $through, $foreignKey, $throughKey, $localKey)
    {
        $this->parent     = $parent;
        $this->model      = $model;
        $this->through    = $through;
        $this->foreignKey = $foreignKey;
        $this->throughKey = $throughKey;
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

        return $this->relation($subRelation)->select();
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
     * @param  mixed  $where 查詢條件（數组或者闭包）
     * @param  mixed  $fields   字段
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
    {}

    /**
     * 预载入关联查詢 返回模型對象
     * @access public
     * @param Model    $result      資料對象
     * @param string   $relation    当前关联名
     * @param string   $subRelation 子关联名
     * @param \Closure $closure     闭包
     * @return void
     */
    public function eagerlyResult(&$result, $relation, $subRelation, $closure)
    {}

    /**
     * 关联统计
     * @access public
     * @param Model    $result  資料對象
     * @param \Closure $closure 闭包
     * @return integer
     */
    public function relationCount($result, $closure)
    {}

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

    /**
     * 执行基础查詢（进执行一次）
     * @access protected
     * @return void
     */
    protected function baseQuery()
    {
        if (empty($this->baseQuery) && $this->parent->getData()) {
            $through      = $this->through;
            $alias        = Loader::parseName(basename(str_replace('\\', '/', $this->model)));
            $throughTable = $through::getTable();
            $pk           = (new $through)->getPk();
            $throughKey   = $this->throughKey;
            $modelTable   = $this->parent->getTable();
            $this->query->field($alias . '.*')->alias($alias)
                ->join($throughTable, $throughTable . '.' . $pk . '=' . $alias . '.' . $throughKey)
                ->join($modelTable, $modelTable . '.' . $this->localKey . '=' . $throughTable . '.' . $this->foreignKey)
                ->where($throughTable . '.' . $this->foreignKey, $this->parent->{$this->localKey});
            $this->baseQuery = true;
        }
    }

}
