<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 校验表达式
 *    校验表达式是由多个(函数调用的)子表达式组成, 格式为 a(1) & b(1,2) && c(3,4) | d(2) . e(1) > f(5)
 *    子表达式之间用运算符连接, 运算符有 & && | || . >
 *    子表达式是函数调用, 格式为 a(1,2)
 *    
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-19 下午3:40:55  
 *
 */
class Sk_Validation_Expression
{
	const OPERATOR_METHOD = array(
		'&' 
	);
	
	/**
	 * 缓存校验表达式编译结果
	 * @var array
	 */
	protected static $_exps = array();
	
	/**
	 * 编译 表达式
	 *     表达式是由多个(函数调用的)子表达式组成, 子表达式之间用运算符连接, 运算符有 & && | || . >
	 * 
	 * @param string $exp
	 * @return boolean
	 */
	public static function compile($exp)
	{
		if(!isset(static::$_exps[$exp]))
		{
			// 编译运算符
			$pattern = '/\s*([&\|\.\>]+)\s*/';
			if(!preg_match_all($pattern, $exp, $matches))
				return FALSE;
			$ops = $matches[1];
			
			// 编译子表达式
			$subexps = preg_split($pattern, $exp);
			return array_map('Validation_Expression::compile_subexp', $subexps);
		}
		
		return static::$_exps[$exp];
	}
	
	/**
	 * 编译 (函数调用的)子表达式
	 *     子表达式是函数调用, 格式为 a(1,2)
	 *     
	 * @param string $subexp
	 * @return boolean
	 */
	public static function compile_subexp($subexp)
	{
		// 编译函数名
		$pattern = '/(\w+)\((.*)\)/';
		if(!preg_match($pattern, $subexp, $matches))
			return array($subexp); // 只有函数名 
		list($_, $func, $args) = $matches;
		
		// 编译参数
		$args = preg_split('/\s*,\s*/', $args); // 根据,分割
		foreach ($args as &$arg) // 去掉字符串的''
			$arg = trim($arg, '\'');
		
		return array($func, $args);
	}
	
	/**
	 * 运算符
	 * @var array
	 */
	protected $_operators;
	
	/**
	 * 子表达式
	 * @var array
	 */
	protected $_subexps;
	
	/**
	 * 构造函数
	 * @param array $operators 运算符
	 * @param array $subexps 子表达式
	 */
	public function __construct(array $operators, array $subexps)
	{
		$this->_operators = $operators;
		$this->_subexps = $subexps;
	}
	
	/**
	 * 执行校验表达式
	 * 
	 * @param unknown $value 要校验的数值
	 * @param array $params 其他参数
	 * @return mixed
	 */
	public function execute($value, $params)
	{
		if(empty($this->_subexps))
			return NULL;
		
		
		
		foreach ($this->_operators as $i => $op)
		{
			
		}
	}
	
	/**
	 * 执行校验表达式
	 *
	 * @param unknown $value 待校验的值
	 * @param array $params 其他参数
	 * @param unknown $result 上衣表达式的结果
	 * @return mixed
	 */
	public function execute_subexp($value, $params, $result)
	{
		list($func, $args) = $this->_subexps[0];
		// 待校验的值为第一个参数
		array_unshift($args, $value);
		// 构建实际参数
		$result = call_user_func_array($func, $args);
	}
}
