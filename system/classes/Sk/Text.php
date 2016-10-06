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
	public static function ucfirst($string, $delimiter = '-')
	{
		return implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
	}
}