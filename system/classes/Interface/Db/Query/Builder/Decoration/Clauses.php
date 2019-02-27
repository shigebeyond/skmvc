<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql修饰子句的模拟构建
 *     每个修饰子句(如where xxx and yyy/group by xxx, yyy)包含多个子表达式(如where可以有多个条件子表达式, 如name='shi', age=1), 每个子表达式有多个元素组成(如name/=/'shi')
 *     每个元素有对应的处理函数
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
interface Interface_Db_Query_Builder_Decoration_Clauses
{
	/**
	 * 编译多个子表达式
	 * @return string
	 */
	public function compile();
	
    /**
     * 添加一个子表达式+连接符
     *
     * @param array $subexp 子表达式
     * @param string $delimiter 当前子表达式的连接符
     * @return Sk_Db_Query_Builder_Expression
     */
  	public function add_subexp(array $subexp, $delimiter = ', ');

    /**
     * 编译一个子表达式
     * @param unknown $subexp
     * @return string
     */
  	public function compile_subexp($subexp);
  
   /**
    * 转换字符串时, 直接编译
    * @return string
    */
	public function __toString();
	
}