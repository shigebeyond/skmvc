<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 3元表达式：主谓宾
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12 上午2:00:45 
 *
 */
class Sk_Db_Query_Expression
{
	/**
	 * 主语
	 * @var string
	 */
	protected $_subject;
	
	/**
	 * 谓语
	 * @var string
	 */
	protected $_predicate; 
	 
	 /**
	  * 宾语
	  * @var string
	  */
	protected $_object;
	
	/**
	 * 构建主谓宾表达式
	 *
	 * @return string
	 */
	public function build()
	{
		// 构建多值宾语
		if(is_array($this->_object))
			$object = $this->build_object($this->_object);
		
		// 主谓宾
		return trim("$this->_subject $this->_predicate $object");
	}
	
	/**
	 * 构建多值宾语
	 * 
	 * @param array $object
	 * @return string
	 */
	public function build_object(array $object, $delimiter = ',', $head = '(', $tail = ')')
	{
		// 分隔符
		$exp = implode($delimiter, $object);
			
		// 头部
		if($head)
			$exp = $head.$exp;
			
		// 尾部
		if($tail)
			$exp .= $tail;
			
		return $exp;
	}
}