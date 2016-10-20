<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 校验方法
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-20 下午2:20:13  
 *
 */
class Sk_Validator
{
	/**
	 * 检查非空
	 * 
	 * @param unknown $value
	 * @return boolean
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
	 * @return boolean
	 */
	public static function length($value, $min, $max = NULL)
	{
		$len = strlen($value);
		return $len >= $min && ($max === NULL ? TRUE : $len <= $max);
	}
}