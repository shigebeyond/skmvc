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
class Sk_Container implements Interface_Container
{
	/**
	 * 组件池: <$name, 组件>
	 * 
	 * @var array
	 */
	protected static $_components = array();
	
	/**
	 * 获得简单的组件
	 * @param string $class
	 * @return multitype:
	 */
	public static function component($class)
	{
		if(!isset(static::$_components[$class]))
			static::$_components[$class] = new $class; // 创建组件
		return static::$_components[$class];
	}
	
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
	public static function component_config($class, $path)
	{
		$name = $class.$path;
		if(!isset(static::$_components[$name]))
		{
			// 判断是否用子类
			if(Text::start_with($path, '_'))
			{
				$class = $name; // 使用子类
				$path = substr($path, 1);
			}
			
			// 获得配置
			$config = Config::load(strtolower($class), $path);
			
			// 创建组件
			static::$_components[$class] = new $class($config, $name);
		}
		return static::$_components[$class];
	}
	
	/**
	 * 删除组件
	 * @param string $name
	 */
	public static function remove_component($name)
	{
		unset(static::$_components[$name]);
	}
}