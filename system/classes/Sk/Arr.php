<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 数组工具类
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-6 上午9:27:56 
 *
 */
class Sk_Arr
{
	/**
	 * 获得数组中的某个元素
	 *
	 *	// 从 $_POST 中获得 key 为 "username" 的元素值
	 *     $username = Arr::get($_POST, 'username');
	 *
	 * @param   array   $array      数组
	 * @param   string  $key        key名
	 * @param   mixed   $default    默认值
	 * @return  mixed
	 */
	public static function get($array, $key, $default = NULL)
	{
		return isset($array[$key]) ? $array[$key] : $default;
	}
	
	/**
	 * 判断数组中的某个元素是否等于指定的值
	 *
	 *	// 判断 $_POST 中获得 key 为 "username" 的元素值是否等于 'shi'
	 *     $username = Arr::equal($_POST, 'username', 'shi');
	 *
	 * @param   array   $array      数组
	 * @param   string  $key        key名
	 * @param   mixed   $other				要等于的值
	 * @return  mixed
	 */
	public static function equal($array, $key, $other, $ignore_case = FALSE)
	{
		if(!isset($array[$key]))
			return FALSE;
		
		if ($ignore_case) 
			return $array[$key] == $other;
		else 
			return strcasecmp($array[$key], $other) == 0; // 忽略大小写的比较，相等为0，大于为1，小于为-1
	}
	
	/**
	 * 使用多级路径来访问多维数组中的值，其中路径是以.为分割
	 *
	 *     // Get the value of $array['foo']['bar']
	 *     $value = Arr::path($array, 'foo.bar');
	 *
	 * @param   array   $array      数组
	 * @param   mixed   $path       多级路径，由多个key组成，以.为分割
	 * @param   mixed   $default    默认值
	 * @param   string  $delimiter  路径的分割符
	 * @return  mixed
	 */
	public static function path($array, $path, $default = NULL, $delimiter = '.')
	{
		// 非数组
		if (!is_array($array))
			return $default;
		
		// 从多级路径中分离出多个key
		$keys = explode($delimiter, $path);
		
		// 遍历key来逐层获得值
		foreach ($keys as $key){
			if (!is_array($array)) // 非数组
				return $default;
			
			if (isset($array[$key])){ // 存在key
				$array = $array[$key]; // 递归到下一级
			}else{
				return $default;
			}
		}
		
		return $array;
	}
	
}