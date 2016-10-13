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
			'filters' => 'column',
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
			'filters' => 'int',
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
	
	public function __construct($db)
	{
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		$this->_where = new Db_Query_Decoratoin_Expression(array('column', 'str', 'value'), 'AND');
		// 字段数组
		$this->_group_by = new Db_Query_Decoratoin_Expression(array('column'));
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		$this->_having = new Db_Query_Decoratoin_Expression(array('column', 'str', 'value'),	'AND');
		// 排序数组, 每个排序 = 字段+方向
		$this->_order_by = new Db_Query_Decoratoin_Expression(array('column', 'order_direction'));
		// 行限数组 limit, offset
		$this->_limit = new Db_Query_Decoratoin_Expression(array('int'));
		// 联表数组，每个联表 = 表名 + 联表方式
		$this->_join = new Db_Query_Decoratoin_Expression(array('table', 'join_type'));
		// 联表条件数组，每个联表条件 = 字段 + 运算符 + 字段
		$this->_on = new Db_Query_Decoratoin_Expression(array('column', 'str', 'column'),	'ON');
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