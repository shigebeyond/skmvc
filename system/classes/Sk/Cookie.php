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
class Sk_Cookie {

	/**
	 * 获得cookie配置
	 * 
	 * @param string $key
	 * @return array|string
	 */
	public static function config($key = NULL){
		$group = 'cookie';
		if($key)
			$group .= ".$key";
		
		return Config::load($group);
	}

	/**
	 * 获得cookie值
	 *
	 *     $theme = Cookie::get('theme', 'blue');
	 *
	 * @param   string  $key        cookie名
	 * @param   mixed   $default    默认值
	 * @return  string
	 */
	public static function get($key, $default = NULL)
	{
		return Arr::get($_COOKIE, $key, $default);
	}

	/**
	 * 设置cookie值
	 *
	 *     static::set('theme', 'red');
	 *
	 * @param   string  $name       cookie名
	 * @param   string  $value      cookie值
	 * @param   integer $expiration 期限
	 * @return  boolean
	 */
	public static function set($name, $value, $expiration = NULL)
	{
		// 获得配置
		$config = static::config();
		
		// 取默认期限
		if ($expiration === NULL)
			$expiration = $config['expiration'];

		// 转为时间戳
		if ($expiration !== 0)
			$expiration += time();

		// 设置cookie
		return setcookie($name, $value, $expiration, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
	}

	/**
	 * 删除cookie
	 *
	 *     static::delete('theme');
	 *
	 * @param   string  $name   cookie名
	 * @return  boolean
	 */
	public static function delete($name)
	{
		// 获得配置
		$config = static::config();
		
		// 删除内存的cookie
		unset($_COOKIE[$name]);

		// 删除客户端的cookie： 让他过期
		return setcookie($name, NULL, -86400, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
	}

}
