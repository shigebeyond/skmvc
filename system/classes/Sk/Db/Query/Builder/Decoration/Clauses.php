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
abstract class Sk_Db_Query_Builder_Decoration_Clauses implements Interface_Db_Query_Builder_Decoration_Clauses
{
	/**
	 * 修饰符， 如where/group by
	 * @var string
	 */
	protected $_operator;
	
	/**
	 * 子表达式, 可视为行
	 * @var array
	 */
	protected $_subexps = array();
	
	/**
	 * 每个元素的处理器, 可视为列的处理
	 * @var array
	 */
	protected $_element_handlers;
	
	public function __construct($operator, array $element_handler)
	{
		$this->_operator = $operator;
		$this->_element_handlers = $element_handler;
	}

	/**
	 * 编译多个子表达式
	 * @return string
	 */
	public function compile()
	{
		if (empty($this->_subexps))
			return ''; // __toString() 必须返回字符串，不能返回NULL
		
		// 逐个子表达式编译+合并
		$subexps = array_map(array($this, 'compile_subexp'), $this->_subexps);
		$sql = implode(' ', $subexps);
		
		if($sql && $this->_operator)
			return $this->_operator.' '.$sql;
		
		return $sql;
	}

    /**
     * 添加一个子表达式+连接符
     *
     * @param array $subexp 子表达式
     * @param string $delimiter 当前子表达式的连接符
     * @return Sk_Db_Query_Builder_Expression
     */
  public abstract function add_subexp(array $subexp, $delimiter = ', ');

    /**
     * 编译一个子表达式
     * @param unknown $subexp
     * @return string
     */
  public abstract function compile_subexp($subexp);
  
  /**
   * 清空
   * @return Db_Query_Builder_Decoration_Clauses
   */
	public function clear() 
	{
		$this->_subexps = array();
		return $this;
	}
  
  /**
   * 转换字符串时, 直接编译
   * @return string
   */
	public function __toString() 
	{
		return $this->compile ();
	}
	
	public function int($value)
	{
		return (int) $value;
	}
	
	public function str($value)
	{
		return $value;
	}
	
	public function order_direction($value)
	{
		if($value !== NULL){
			$value = strtoupper($value);
			if (in_array($value, array('ASC', 'DESC')))
				return $value;
		}
		return NULL;
	}
	
}