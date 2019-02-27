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
interface Interface_Loader
{
	/**
	 * 插入顶级目录
	 * @param string $path
	 */
	public static function add_path($path);

	/**
	 * 查找文件
	 *
	 * @param string $dir 相对目录
	 * @param string $file 文件名
	 * @param string $ext 文件扩展名，默认为php
	 * @return string|boolean 文件的绝对路径
	*/
	public static function find_file($dir, $file, $ext = 'php');
	
	/**
	 * 加载文件
	 *
	 * @param string $dir 相对目录
	 * @param string $file 文件名
	 * @param string $default 默认值
	 * @return string|boolean 文件中返回的数据
	*/
	public static function load_file($dir, $file, $default = NULL);
	
	/**
	 * 加载类
	 * 	TODO：支持命名空间
	 *
	 * @param string $class
	 * @return boolean
	 */
	public static function load_class($class);
}