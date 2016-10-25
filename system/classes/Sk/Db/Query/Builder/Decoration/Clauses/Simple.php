<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 简单的(sql修饰)子句
 * 
 * 	在子表达式的拼接中，何时拼接连接符？
 *     1 在compile()时拼接连接符 => 你需要单独保存每个子表达式对应的连接符，在拼接时取出
 *     2 在add_subexp()就将连接符也记录到子表达式中 => 在compile()时直接连接子表达式的内容就行，不需要关心连接符的特殊处理
 *     我采用的是第二种
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
class Sk_Db_Query_Builder_Decoration_Clauses_Simple extends Db_Query_Builder_Decoration_Clauses implements Interface_Db_Query_Builder_Decoration_Clauses_Simple
{
	/**
	 * 添加一个子表达式+连接符
	 *
	 * @param array $subexp 子表达式
	 * @param string $delimiter 当前子表达式的连接符
	 * @return Sk_Db_Query_Builder_Expression
	 */
	public function add_subexp(array $subexp, $delimiter = ', ') {
		// 将连接符也记录到子表达式中, 忽略第一个子表达式的连接符 => 编译好子表达式直接拼接就行
		if (! empty ( $this->_subexps )) 
		{
			// 有可能子表达式的size < 元素处理器的size(如limit表达式), 因此以元素处理器的size为准
			$size = count ( $this->_element_handlers );
			$subexp [$size] = $delimiter;
		}
		
		$this->_subexps [] = $subexp;
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
		// 遍历处理器来处理对应元素, 没有处理的元素也直接拼接
		foreach($this->_element_handlers as $i => $handler) 
		{
			// 处理某个元素的值
			if(isset($subexp[$i])) // 有可能子表达式的size < 元素处理器的size(如limit表达式)
			{
				if (is_callable($handler)) // 自定义处理函数
					$subexp [$i] = call_user_func($handler, $subexp[$i]);
				else // 内部方法
					$subexp [$i] = $this->$handler($subexp[$i]);
			}
		}
		
		return implode(' ', $subexp); // 用空格拼接多个元素
	}

}