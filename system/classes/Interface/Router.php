<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 路由器
 *	1 加载路由规则
 *	2 解析路由：匹配规则
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-6 上午12:01:17
 *
 */
interface Interface_Router
{
	/**
	 * 加载/获得路由规则
	 */
	public static function routes();
	
	/**
	 * 解析路由：匹配规则
	 * @param string $uri
	 * @return array|boolean [路由参数, 路由规则]
	 */
	public static function parse($uri);	

}