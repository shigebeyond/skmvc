<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * join表达式
 * 	由于join的语法比较特别，是故特殊处理
 *  1 on： 直接继承Db_Query_Builder_Decoration_Expression_Group
 *  2 join：单独处理表名+联表类型
 *  
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
class Sk_Db_Query_Builder_Decoration_Expression_Join extends Db_Query_Builder_Decoration_Expression_Group
{
	/**
	 * 联表类型：inner/left/right
	 * @var string
	 */
	protected $_type;
	
	/**
	 * 联查的表名
	 * @var string
	 */
	protected $_table;
	
	public function __construct($db, $table, $type)
	{
		parent::__construct($db, 'ON', array('column', 'str', 'column'));
		$this->_table = $table;
		$this->_type = $type;
	}

    	/**
	 * 编译多个子表达式
	 * @return string
	 */
	public function compile()
	{
		// join表达式
		$join = ' JOIN '.$this->table($this->_table);
		if($this->_type)
			$join = ' '.$this->_join_type($this->_type).$join;
		
		// on表达式
		$on = parent::compile();
		return $join.' '.$on;
	}

}