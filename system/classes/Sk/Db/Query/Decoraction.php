<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- 修饰子句: where/group by/order by/limit
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
	 * 修饰子句模板
	 * @var string
	 */
	protected $_decorate_templates = array(
		 // 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		'where' => array(':column :str :value', 'AND'), // 模板 + 分隔符
		 // 字段数组
		'group_by' => array(':column'),
		 // 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		'having' => array(':column :str :value', 'AND'),
		 // 排序数组, 每个排序 = 字段+方向
		'order_by' => array(':column :direction'),
		 // 行限数组: limit, offset
		'limit' => array(':int'),
	);
	
	public function compile_decoration()
	{
		foreach ($this->_decorate_templates as $key => $template)
		{
			$rows = $this->{"_$key"};
			foreach ($rows as $row)
			{
				
			}
		}
	}
}