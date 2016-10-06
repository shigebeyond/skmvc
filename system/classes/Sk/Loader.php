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
	 * 插入顶级目录
	 * @param string $path
	 */
	public static function add_path($path){
		static::$_paths[] = $path;
	}

	/**
	 * 查找文件
	 * 	TODO： 支持缓存
	 *
	 * @param string $dir 相对目录
	 * @param string $file 文件名
	 * @param string $ext 文件扩展名，默认为php
	 * @return string|boolean 文件的绝对路径
	*/
	public static function find($dir, $file, $ext = 'php'){
		// 相对路径
		$relative_path = $dir.DIRECTORY_SEPARATOR.$file.'.'.$ext;
		
		// 1 遍历顶级目录，找查找文件
		foreach (static::$_paths as $top_path){
			if(is_file($path = $top_path.$relative_path))
				return $path;
		}
		
		// 2 在系统目录下查找文件
		if(is_file($path = SYSPATH.$relative_path))
			return $path;
		
		return false;
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
		if ($path = static::find('classes', $file))
		{
			// 加载类文件
			require $path;
			return TRUE;
		}

		return FALSE;
	}
}