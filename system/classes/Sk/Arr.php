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
		if (!static::is_array($array))
			return $default;
		
		// 从多级路径中分离出多个key
		$keys = explode($delimiter, $path);
		
		// 遍历key来逐层获得值
		foreach ($keys as $key){
			if (!static::is_array($array)) // 非数组
				return $default;
			
			if (isset($array[$key])){ // 存在key
				$array = $array[$key]; // 递归到下一级
			}else{
				return $default;
			}
		}
		
		return $array;
	}
	
	/**
	 * 使用多级路径来设置多维数组中的值，其中路径是以.为分割
	 *
	 *     // Set the value of $array['foo']['bar']
	 *     Arr::set_path($array, 'foo.bar', 'shi');
	 *
	 * @param   array   $array      数组
	 * @param   mixed   $path       多级路径，由多个key组成，以.为分割
	 * @param   mixed   $value    		要设置的元素值
	 * @param   string  $delimiter  路径的分割符
	 * @return  mixed
	 */
	public static function set_path(&$array, $path, $value, $delimiter = '.')
	{
		// 1 获得路径中的key
		if(is_array($path))
			$keys = $path;
		else // 从多级路径中分离出多个key
			$keys = explode($delimiter, $path);
		
		// 2 准备好路径
		$last_key = array_pop($keys);
		$array = &self::_prepare_path($array, $keys, $delimiter);

		// 3 设置最后一层的元素值
		if($last_key === '') // 如果最后一个key是空字符串, 则直接追加
			$array[] = $value;
		else
			$array[$last_key] = $value;
	}
	
	/**
	 * 为多维数组准备好路径中的元素
	 * 
	 * @param   array   $array      数组
	 * @param   mixed   $keys       多级路径
	 * @return array 准备好的末尾元素
	 */
	 protected static function &_prepare_path(&$array, array $keys) 
	 {
		// 遍历key来创建中间层
		foreach ($keys as $key)
		{
			if (!isset($array[$key]))
				$array[$key] = array();
			
			$array = &$array[$key];
		}
		
		return $array;
	}

	
	/**
	 * 判断是否数组
	 * 
	 * @param array $array
	 * @return boolean
	 */
	public static function is_array($array)
	{
		return is_array($array) || $array instanceof ArrayAccess || $array instanceof Traversable;
	}
	
	/**
	 * 判断是否关联数组
	 *
	 * @param   array   $array
	 * @return  boolean
	 */
	public static function is_assoc(array $array)
	{
		$keys = array_keys($array);
	
		// If the array keys of the keys match the keys, then the array must
		// not be associative (e.g. the keys array looked like {0:0, 1:1...}).
		return array_keys($keys) !== $keys;
	}
	
	/**
	 * 将多个对象中的指定的key字段与value字段给抽取为关联数组
	 *   如果 $value_field 不为空, 则建议直接使用 array_column($value_field, $key_field);
	 * 
	 * @param array $arr
	 * @param string $key_field 
	 * @param string $value_field
	 * @return array
	 */
	public static function assoc(array $arr, $key_field = NULL, $value_field = NULL)
	{
		$result = array();
		foreach ($arr as $i => $item)
		{
			$key = $key_field === NULL ? $i : $item->$key_field;
			$value = $value_field === NULL ? $item : $item->$value_field;
			$result[$key] = $value;
		}
		return $result;
	}
	
	/* public function set($arr, $key, $value = NULL)
	{
		if(is_array($key))
			$data = $key;
		else
			$data[$key] = $value;
	} */
	
	/**
	 * 过滤数组中的某个元素值
	 * 
	 * @param array $data
	 * @param   string $key    参数名
	 * @param   string $default  参数默认值
	 * @param   string $filter  参数过滤表达式, 如 "trim > htmlspecialchars"
	 * @return mixed
	 */
	public static function filter_value(array $data, $key = NULL, $default = NULL, $filter_exp = NULL)
	{
		// 获得全部元素
		if ($key === NULL)
			return $data;
	
		// 获得单个元素
		$value = Arr::path($data, $key, $default);
		
		// 过滤元素值
		if($filter_exp)
			$value = Validation::execute($filter_exp, $value, $data);
		
		return $value;
	}
	
}
