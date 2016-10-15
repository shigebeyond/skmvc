<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 文本工具类
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-6 上午9:27:56 
 *
 */
class Sk_Text
{
	/**
	 * 将(以某个分隔符分割的)多个单词进行首字母大写
	 *
	 *      $str = Text::ucfirst('work_flow'); // returns "Work_Flow"
	 *
	 * @param   string  $string     要转换的字符串，包含以某个分隔符分割的多个单词
	 * @param   string  $delimiter  分隔符
	 * @return  string
	 */
	public static function ucfirst($string, $delimiter = '_')
	{
		return implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
	}
	
	/**
	 * 获得子串的位置
	 * 
	 * @param string $str 被找的字符串
	 * @param string $substr 要找的子字符串
	 * @param string $ignore_case 忽略大小写
	 * @return boolean
	 */
	public static function pos($str, $substr, $ignore_case = FALSE)
	{
		return $ignore_case ? stripos($str, $substr) : strpos($str, $substr);
	}
	
	/**
	 * 判断 $str 是否包含 $substr
	 * 
	 * @param string $str 被找的字符串
	 * @param string $substr 要找的子字符串
	 * @param string $ignore_case 忽略大小写
	 * @return boolean
	 */
	public static function contains($str, $substr, $ignore_case = FALSE)
	{
		return static::pos($str, $substr, $ignore_case) !== FALSE; // pos不为false
	}
	
	/**
	 * 判断 $str 是否以 $substr 开头
	 * 
	 * @param string $str 被找的字符串
	 * @param string $substr 要找的子字符串
	 * @param string $ignore_case 忽略大小写
	 * @return boolean
	 */
	public static function start_with($str, $substr, $ignore_case = FALSE)
	{
		return static::pos($str, $substr, $ignore_case) === 0; // pos为0
	}
	
	/**
	 * 判断 $str 是否以 $substr 结尾
	 * 
	 * @param string $str 被找的字符串
	 * @param string $substr 要找的子字符串
	 * @param string $ignore_case 忽略大小写
	 * @return boolean
	 */
	public static function end_with($str, $substr, $ignore_case = false)
	{
		$end_pos = strlen($str) - strlen($substr);
		return static::pos($str, $substr, $ignore_case) === $end_pos; // pos为$end_pos
	}
}