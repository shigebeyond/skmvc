<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 容器
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-14
 *
 */
interface Interface_Container 
{
	/**
	 * 获得简单的组件
	 * @param string $class
	 * @return multitype:
	 */
	public static function component($class);
	
	/**
	 * 获得(读取配置文件)的组件
	 * 
	 * @param string $class
	 * @param string $path 配置的相对路径
	 * 		其中配置的绝对路径为  $class.'.'.$path
	 * 		如果第一个字符是'_', 表示要使用子类对象, 子类名为 $class.$config 
	 * 
	* @return multitype:
	 */
	public static function component_config($class, $path);
	
	/**
	 * 删除组件
	 * @param string $name
	 */
	public static function remove_component($name);
}