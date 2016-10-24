<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 校验表达式
 *    校验表达式是由多个(函数调用的)子表达式组成, 格式为 a(1) & b(1,2) && c(3,4) | d(2) . e(1) > f(5)
 *    子表达式之间用运算符连接, 运算符有 & && | || . >
 *    子表达式是函数调用, 格式为 a(1,2)
 *    运算符的含义: 
 *        & 与  
 *        && 短路与  
 *        | 或  
 *        || 短路或 
 *         .  字符串连接  
 *         > 累积结果运算
 *   无意于实现完整语义的布尔表达式, 暂时先满足于输入校验与orm保存数据时的校验, 因此: 
 *       运算符没有优先级, 只能按顺序执行, 不支持带括号的子表达式
 *    
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-19 下午3:40:55  
 *
 */
class Sk_Validation_Expression
{
	/**
	 * 运算符的正则
	 * @var string
	 */
	const REGEX_OPERATOR = '/\s*([&\|\.\>]+)\s*/';
	
	/**
	 * 函数的正则
	 * @var string
	 */
	const REGEX_FUNC = '/(\w+)\((.*)\)/';
	
	/**
	 * 编译 表达式
	 *     表达式是由多个(函数调用的)子表达式组成, 子表达式之间用运算符连接, 运算符有 & && | || . >
	 * 
	 * @param string $exp
	 * @return boolean
	 */
	public static function compile($exp)
	{
		// 编译运算符
		if(!preg_match_all(static::REGEX_OPERATOR, $exp, $matches))
			return FALSE;
		$ops = $matches[1];
		
		// 编译子表达式
		$subexps = preg_split(static::REGEX_OPERATOR, $exp);
		$subexps = array_map('Validation_Expression::compile_subexp', $subexps);
		
		return array($ops, $subexps);
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
		if(!preg_match(static::REGEX_FUNC, $subexp, $matches))
			return array($subexp, array()); // 只有函数名 
		list($_, $func, $params) = $matches;
		
		// 编译参数
		$params = preg_split('/\s*,\s*/', $params); // 根据,分割
		foreach (array_keys($params) as $i) // 逐个修正参数
		{
			$param = $params[$i];
			switch($param[0])
			{
				case '\'': // 对字符串：去掉字符串的''
					$params[$i] = trim($param, '\'');
					break;
				case ':': // 对变量：变量名作为key，如":name"，则变量名为"name"
					$params[substr($param, 1)] = $param;
					unset($params[$i]);
					break;
			}
		}
		
		return array($func, $params);
	}
	
	/**
	 * 运算符的数组， 其长度 = 子表达式数组长度 - 1
	 * @var array
	 */
	protected $_operators;
	
	/**
	 * 子表达式的数组，其长度 = 运算符数组长度 + 1
	 *   一个子表达式 = array(函数名，参数数组)
	 *   参数数组 = array(1, 2, 'name' => ':name') 当参数为值时，key为int，当参数为变量（如":name"）时，key为变量名（如"name"）
	 * @var array
	 */
	protected $_subexps;
	
	/**
	 * 构造函数
	 * @param string $exp 校验表达式字符串
	 */
	public function __construct($exp)
	{
		list($operators, $subexps) = static::compile($exp);
		$this->_operators = $operators;
		$this->_subexps = $subexps;
	}
	
	/**
	 * 执行校验表达式
	 * 
	 * @param unknown $value 要校验的数值，该值可能被修改
	 * @param array|ArrayAccess $data 其他参数
	 * @param array $last_subexp 短路时的最后一个子表达式
	 * @return mixed
	 */
	public function execute(&$value, $data = NULL, array &$last_subexp = NULL)
	{
		if(empty($this->_subexps))
			return NULL;
		
		// 逐个运算子表达式
		$result;
		foreach ($this->_subexps as $i => $subexp)
		{
			// 1 运算子表达式
			$curr = $this->execute_subexp($subexp, $value, $data);
			
			// 2 处理结果
			$next_op = Arr::get($this->_operators, $i);
			// 2.1 累积结果运算: 当前结果 $result 作为下一参数 $value
			if($next_op === '>')
			{
				$value = $result = $curr;
				continue;
			}
			
			if($i === 0) // 2.2 第一个子表达式
			{
				// 下一个运算符
				if($next_op == '&&' && !$curr || $next_op == '||' && $curr) // 短路
				{
					$last_subexp = $subexp;
					return $curr;
				}
				$result = $curr;
			}
			else // 2.3 其他子表达式
			{
				// 当前运算符
				$op = $this->_operators[$i-1];
				switch ($op)
				{
					case '&': // 与
						$result = $result && $curr;
						break;
					case '&&': // 短路与
						$result = $result && $curr;
						if(!$result)// 短路
						{
							$last_subexp = $subexp;
							return $result;
						}
						break;
					case '|': // 或
						$result = $result || $curr;
						break;
					case '||': // 短路或
						$result = $result || $curr;
						if($result)// 短路
						{
							$last_subexp = $subexp;
							return $result;
						}
						break;
					case '.': // 字符串连接
						$result .= $curr;
						break;
					default:
						$result = $curr;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * 执行校验表达式
	 *
	 * @param array $subexp 子表达式
	 * @param unknown $value 待校验的值
	 * @param array|ArrayAccess $binds 变量
	 * @return mixed
	 */
	public function execute_subexp($subexp, $value, $binds = NULL)
	{
		list($func, $params) = $subexp;
		// 实际参数
		if (!empty($binds)) 
		{
			foreach ($params as $i => $param)
			{
				if(is_string($i)) // 根据变量名，来替换变量值
					$params[$i] = Arr::get($binds, $i);
			}
		}
		// 待校验的值: 作为第一参数
		array_unshift($params, $value);
		// 调用函数
		if(method_exists('Validation', $func)) // 优先调用 Validator 中的校验方法
			$func = 'Validation::'.$func;
		return call_user_func_array($func, $params);
	}
}
