<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 加载器，负责加载文件与类
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-6 上午12:01:17
 *
 */
class Sk_Loader{
	
	/**
	 * 顶级目录
	 * @var array 
	 */
	protected static $_paths = array(APPPATH, /* SYSPATH */); // 排除系统目录，因为要支持动态插入顶级目录
	
	/**
	 * 缓存文件的路径: <文件名/相对路径 => 绝对路径>
	 * 	因为有多个顶级目录, 为避免每次都遍历顶级目录来查找文件, 因此缓存已找到的文件的路径
	 * @var  array
	 */
	protected static $_files = array();
	
	/**
	 * 插入顶级目录
	 * @param string $path
	 */
	public static function add_path($path){
		static::$_paths[] = $path;
	}

	/**
	 * 查找文件
	 *
	 * @param string $dir 相对目录
	 * @param string $file 文件名
	 * @param string $ext 文件扩展名，默认为php
	 * @return string|boolean 文件的绝对路径
	*/
	public static function find_file($dir, $file, $ext = 'php'){
		// 相对路径
		$relative_path = $dir.DIRECTORY_SEPARATOR.$file.'.'.$ext;
		
		// 先查缓存
		if(!isset(static::$_files[$relative_path]))
			static::$_files[$relative_path] = static::_find_file($relative_path); // 再查系统
		
		return static::$_files[$relative_path];
	}
	
	/**
	 * 根据文件的相对路径来确定绝对路径
	 * 
	 * @param string $relative_path 相对路径
	 * @return string|boolean 绝对路径
	 */
	protected static function _find_file($relative_path)
	{
		// 1 遍历顶级目录，找查找文件
		foreach (static::$_paths as $top_path){
			if(is_file($path = $top_path.$relative_path))
				return $path;
		}
		
		// 2 在系统目录下查找文件
		if(is_file($path = SYSPATH.$relative_path))
			return $path;
		
		return FALSE;
	}
	
	/**
	 * 加载文件
	 *
	 * @param string $dir 相对目录
	 * @param string $file 文件名
	 * @param string $default 默认值
	 * @return string|boolean 文件中返回的数据
	*/
	public static function load_file($dir, $file, $default = NULL){
		//先find，后include
		$path = static::find_file($dir, $file);

		if(!$path)
			return $default;

		return include $path;
	}
	
	/**
	 * 加载类
	 * 	TODO：支持命名空间
	 *
	 * @param string $class
	 * @return boolean
	 */
	public static function load_class($class){
		//类名转文件名：直接将类名连接符_变为/路径连接符
		$file = str_replace('_', DIRECTORY_SEPARATOR, $class);

		// 在classes目录下查找类文件
		if ($path = static::find_file('classes', $file))
		{
			// 加载类文件
			require $path;
			return TRUE;
		}

		return FALSE;
	}
}