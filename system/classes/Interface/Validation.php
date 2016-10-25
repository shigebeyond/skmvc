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
interface Interface_Validation
{
	/**
	 * 编译与执行校验表达式
	 *
	 * @param string $exp 校验表达式
	 * @param unknown $value 要校验的数值，该值可能被修改
	 * @param array|ArrayAccess $data 其他参数
	 * @param string message
	 * @return mixed
	 */
	public static function execute($exp, &$value, $data = NULL, &$message = NULL);
	
}