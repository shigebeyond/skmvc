<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Cookie工具类
 * 	TODO: 添加cookie的加密
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-8 上午12:52:35
 *
 */
interface Interface_Cookie
{

	/**
	 * 获得cookie值
	 *
	 * <code>
	 *     $theme = Cookie::get('theme', 'blue');
	 * </code>
	 *
	 * @param   string  $key        cookie名
	 * @param   mixed   $default    默认值
	 * @return  string
	 */
	public static function get($key = NULL, $default = NULL);

	/**
	 * 设置cookie值
	 *
	 * <code>
	 *     static::set('theme', 'red');
	 * </code>
	 *
	 * @param   string  $name       cookie名
	 * @param   string  $value      cookie值
	 * @param   integer $expiration 期限
	 */
	public static function set($name, $value = NULL, $expiration = NULL);

	/**
	 * 删除cookie
	 *
	 * <code>
	 *     static::delete('theme');
	 * </code>
	 *
	 * @param   string  $name   cookie名
	 * @return  boolean
	 */
	public static function delete($name);

}
