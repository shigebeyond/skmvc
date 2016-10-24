<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 校验器
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-20 下午2:20:13  
 *
 */
class Sk_Validation
{
	/**
	 * 校验方法对应的错误消息
	 * @var array
	 */
	protected static $_messages = array(
		'not_empty' => '不能为空',
		'length' => '的长度必须在:0到:1之间',
		'range' => '的数值必须是:0到:1之间的整数',
	);
	
	/**
	 * 缓存编译后的表达式
	 * @var array
	 */
	protected static $_exps_cached = array();
	
	/**
	 * 编译与执行校验表达式
	 *
	 * @param string $exp 校验表达式
	 * @param unknown $value 要校验的数值，该值可能被修改
	 * @param array|ArrayAccess $data 其他参数
	 * @param string message
	 * @return mixed
	 */
	public static function execute($exp, &$value, $data = NULL, &$message = NULL)
	{
		if(!$exp)
			return $value;
		
		// 编译
		if(!isset(static::$_exps_cached[$exp]))
			static::$_exps_cached[$exp] = new Validation_Expression($exp);
		
		// 执行
		$result = static::$_exps_cached[$exp]->execute($value, $data, $last_subexp);
		
		// 构建结果消息
		if(!$result)
			$message = static::message($last_subexp);
		
		return $result;
	}
	
	/**
	 * 构建结果消息
	 * @param array $last_subexp array($func, $params)
	 * @return string
	 */
	public static function message($last_subexp)
	{
		list($func, $params) = $last_subexp;
		if (isset(static::$_messages[$func])) 
			return Text::replace(static::$_messages[$func], $params);
		
		return "校验[$func]规则失败";
	}
	
	/**
	 * 检查非空
	 * 
	 * @param unknown $value
	 * @return bool
	 */
	public static function not_empty($value)
	{
		return !empty($value);
	}
	
	/**
	 * 检查长度
	 * 
	 * @param unknown $value
	 * @param int $min
	 * @param int $max
	 * @return bool
	 */
	public static function length($value, $min, $max = NULL)
	{
		$len = strlen($value);
		return $len >= $min && ($max === NULL ? TRUE : $len <= $max);
	}
	
	/**
	 * 检查是否在某个范围内
	 *
	* @param string $value
	* @param int $min 最小值
	* @param int $max 最大值
	* @param int $step 步长
	* @return  bool
	*/
	public static function range($value, $min, $max, $step = 1)
	{
		return ($value >= $min && $value <= $max) // 是否在范围内
						&& (($value - $min) % $step === 0); // 是否间隔指定步长
	}
	
	/**
	 * 检查是否邮件格式
	 * @param unknown $value
	 * @return bool
	 */
	public static function email($value)
	{
		return preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $value);
	}
	
}