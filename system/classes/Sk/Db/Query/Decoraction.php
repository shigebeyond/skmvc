<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- 修饰子句: 由修饰符where/group by/order by/limit来构建的子句
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
abstract class Sk_Db_Query_Decoration extends Db_Query 
{
	/**
	 * 关于每类修饰符的配置：过滤规则（转换单个值） + 拼接分割符（转换多个值）
	 * @var array
	 */
	protected static $_decoration_config = array(
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		'where' => array(
			'filters' => array('column', 'str', 'value'), // 过滤规则
			'delimiter' => 'AND' // 分隔符
		),
		// 字段数组
		'group_by' => array(
			'filters' => array('column'),
		),
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		'having' => array(
			'filters' => array('column', 'str', 'value'),
			'delimiter' => 'AND'
		),
		// 排序数组, 每个排序 = 字段+方向
		'order_by' => array(
			'filters' => array('column', 'order_direction')
		),
		// 行限数组 limit, offset
		'limit' => array(
			'filters' => array('int'),
		),
		// 联表数组，每个联表 = 表名 + 联表方式
		'join' => array(
			'filters' => array('table', 'join_type'),
		),
		// 联表条件数组，每个联表条件 = 字段 + 运算符 + 字段
		'on' => array(
			'filters' => array('column', 'str', 'column'),
			'delimiter' => 'ON'
		)
	);
	
	/**
	 * 过滤器
	 * @var Db_Query_Decoration_Filter
	 */
	protected $_filter;
	
	/**
	 * 获得过滤器
	 * @return Db_Query_Decoration_Filter
	 */
	public function filter()
	{
		if($this->_filter === NULL)
			$this->_filter = new Db_Query_Decoration_Filter($this->_db);
		
		return $this->_filter;
	}
	
	/**
	 * 对某个数据 执行过滤规则
	 * 
	 * @param array $rules
	 * @param array $arr
	 * @return array
	 */
	public function run_filter(array $rules, array $arr)
	{
		// 逐个规则过滤，一个规则过滤其中一个值
		$n = count($rules);
		for ($i = 0; $i < $n; $i++)
		{
			$rule = $rules[$i]; // 规则
			$value = Arr::get($arr, $i); // 值
			$arr[$i] = $this->filter()->$rule($value); // 过滤
		}
		return $arr;
	}
	
	/**
	 * 编译修饰子句
	 * @return string
	 */
	public function compile_decoration()
	{
		// 实际上是过滤子句，过滤子句中的参数
		$sql = '';
		
		// 遍历每类修饰的模板
		foreach ($this->_decoration_config as $decoration => $config)
		{
			extract($config);
			// 1 执行过滤
			$rows = array_map(function($row) use($filters){
				return $this->run_filter($filters, $row);
			}, $this->{"_$decoration"});
			
			// 2 合并
			$delimiter = isset($delimiter) ? $delimiter : ', ';
			$sql .= implode($delimiter, $rows); 
		}
		
		return $sql;
	}
	
}