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
abstract class Sk_Db_Query_Builder_Decoration extends Db_Query_Builder_Action 
{
	
	/**
	 * 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
	 * @var array
	 */
	protected $_where;
	
	/**
	 * 字段数组
	 * @var array
	 */
	protected $_group_by;
	
	/**
	 * 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
	 * @var array
	 */
	protected $_having;
	
	/**
	 * 排序数组, 每个排序 = 字段+方向
	 * @var array
	 */
	protected $_order_by;
	
	/**
	 * 行限数组 limit, offset
	 * @var array
	 */
	protected $_limit;
	
	/**
	 * 联表数组，每个联表 = 表名 + 联表方式
	 * @var array
	 */
	protected $_join;
	
	/**
	 * 联表条件数组，每个联表条件 = 字段 + 运算符 + 字段
	 * @var array
	 */
	protected $_on;

	public function __construct($db, $table = NULL)
	{
		parent::__construct($db, $table);
		
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		$this->_where = new Db_Query_Builder_Decoratoin_Expression($this->_db, array('column', 'str', 'value'), 'AND');
		// 字段数组
		$this->_group_by = new Db_Query_Builder_Decoratoin_Expression($this->_db, array('column'));
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		$this->_having = new Db_Query_Builder_Decoratoin_Expression($this->_db, array('column', 'str', 'value'),	'AND');
		// 排序数组, 每个排序 = 字段+方向
		$this->_order_by = new Db_Query_Builder_Decoratoin_Expression($this->_db, array('column', 'order_direction'));
		// 行限数组 limit, offset
		$this->_limit = new Db_Query_Builder_Decoratoin_Expression($this->_db, array('int'));
		// 联表数组，每个联表 = 表名 + 联表方式
		$this->_join = new Db_Query_Builder_Decoratoin_Expression($this->_db, array('table', 'join_type'));
		// 联表条件数组，每个联表条件 = 字段 + 运算符 + 字段
		$this->_on = new Db_Query_Builder_Decoratoin_Expression($this->_db, array('column', 'str', 'column'),	'ON');
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