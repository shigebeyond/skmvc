<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 视图
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-21 下午3:14:54  
 *
 */
interface Interface_View
{
	/**
	 * 设置全局变量
	 * @param string $key
	 * @param mixed $value
	 * @return View
	 */
	public function set_global($key, $value);
	
	/**
	 * 设置局部变量
	 * @param string $key
	 * @param mixed $value
	 * @return View
	 */
	public function set($key, $value);
	
	/**
	 * 渲染视图
	 * 
	 * @return string
	 */
	public function render();
}