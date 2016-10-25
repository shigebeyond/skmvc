<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 分组子句
 * 	子句中有子句, 而第二层的子句由分组来管理
 *     一个分组有多个子句, 就是使用()来包含的子句
 *     
 *  实现:
 *     子表达式还是子句
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
class Sk_Db_Query_Builder_Decoration_Clauses_Group extends Db_Query_Builder_Decoration_Clauses implements Interface_Db_Query_Builder_Decoration_Clauses_Group
{
	/**
	 * 开启一个分组
	 * 
	 * @param	$delimiter
	 * @return Sk_Db_Query_Builder_Decoration_Clauses_Group
	 */
	public function open($delimiter) 
	{
		// 将连接符也记录到子表达式中, 忽略第一个子表达式
		$subexp = '(';
		if (! empty ( $this->_subexps ))
			$subexp = $delimiter . $subexp;
		
		$this->_subexps [] = $subexp;
		return $this;
	}
	
	/**
	 * 结束一个分组
	 * 
	 * @return Sk_Db_Query_Builder_Decoration_Clauses_Group
	 */
	public function close() 
	{
		$this->_subexps [] = ')';
		return $this;
	}
	
	/**
	 * 获得最后一个子表达式
	 * 
	 * @return Db_Query_Builder_Decoration_Clauses
	 */
	public function end_subexp() 
	{
		$last = end($this->_subexps);
		if (! $last instanceof Db_Query_Builder_Decoration_Clauses_Simple)
			$this->_subexps[] = $last = new Db_Query_Builder_Decoration_Clauses_Simple ( $this->_db, NULL, $this->_element_handlers );
		return $last;
	}
	
	/**
	 * 添加子表达式
	 * 
	 * @param array $subexp        	
	 * @param string $delimiter        	
	 * @return Sk_Db_Query_Builder_Decoration_Clauses_Group
	 */
	public function add_subexp(array $subexp, $delimiter = ', ') 
	{
		// 代理最后一个子表达式
		$this->end_subexp()->add_subexp ( $subexp, $delimiter );
		return $this;
	}
	
	/**
	 * 编译一个子表达式
	 * 
	 * @param array $subexp        	
	 * @return string
	 */
	public function compile_subexp($subexp) 
	{
		// 子表达式是: string / Sk_Db_Query_Builder_Decoration_Clauses_Simple
		// Sk_Db_Query_Builder_Decoration_Clauses_Simple 转字符串自动compile
		return "$subexp";
	}

}