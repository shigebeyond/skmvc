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
	 * 获得数组中的一个值
	 *
	 *     // Get the value "username" from $_POST, if it exists
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