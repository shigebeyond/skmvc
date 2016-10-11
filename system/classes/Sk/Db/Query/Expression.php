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
	public static $config = array(
		'select' => [','],
		'from' => NULL,
		'where' => [],
		'limit' => [' '],
		'order_by' => [','],
		'group_by' => [','],
		'where_group' => ['AND', '(', ')'],
		'or_where_group' => ['AND', '(', ')'],
		'having' => ['AND', '(', ')'],
	);
	
	public static function instance($type)
	{
		$arr = array();
		$arr['select'] = new static(',');
		$arr['from'] = new static();
		
	}
	
	/**
	 * 分割符
	 * @var string
	 */
	protected $_delimiter;
	
	/**
	 * 头部
	 * @var string
	 */
	protected $_head;
	
	/**
	 * 尾部
	 * @var string
	 */
	protected $_tail;
	
	
	public function __construct($delimiter, $head = NULL, $tail = NULL)
	{
		$this->_delimiter = $delimiter;
		$this->_head = $head;
		$this->_tail = $tail;
	}
	
	/**
	 * 生成主谓宾表达式
	 * 
	 * @param string $predicate 谓语
	 * @param string $object 宾语
	 * @param string $subject 主语
	 * @return string
	 */
	public function build($predicate, $object, $subject = NULL)
	{
		// 谓语 + 宾语
		$exp = $predicate.' '.$object;
		
		// 主语
		if ($subject) 
			$exp = $subject.' '.$exp;
		
		return $exp;
	}
	
	/**
	 * 生成宾语表达式
	 * 
	 * @param unknown $object
	 * @return string
	 */
	public function build_object($object)
	{
		// 对数组类型
		if(is_array($object))
		{
			$exp = implode($this->_delimiter, $object);
			
			if($this->_head)
				$exp = $this->_head.$exp;
			
			if($this->_tail)
				$exp .= $this->_tail;
			
			return $exp;
		}
		
		return $object;
	}
}