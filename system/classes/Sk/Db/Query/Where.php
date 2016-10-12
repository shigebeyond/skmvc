<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- action
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
abstract class Sk_Db_Query_Where extends Db_Query 
{
	/**
	 * where子句模板
	 * @var string
	 */
	protected $_where_template = 'WHERE :where GROUP BY :group_by HAVING :having ORDER BY :order_by LIMIT :limit';
	
	/**
	 * 编译where子句
	 * @see Sk_Db_Query::compile_where()
	 */
	public function compile_where()
	{
		// 解析谓句格式: 操作+值
		preg_match_all('/([^:]+) :(\w+)/', $this->_where_template, $matches);
		$operators = $matches[1]; // 操作
		$values = $matches[2]; // 值
		$n = count($operators);
		$sql = '';
		for ($i = 0; $i < $n; $i++)
		{
			
			$sql .= $operators[$i].' '.$values[$i].' ';
		}
		
		return rtrim($sql);
	}

	
	public function compile_param($param)
	{
		// group_by 与 order_by 是一样的, 直接拼接数组
		
		// where 与 having 是一样的
	}
}