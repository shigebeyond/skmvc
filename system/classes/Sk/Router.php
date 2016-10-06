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
class Sk_Router extends Singleton_Configurable{
	
	/**
	 * 全部路由规则
	 * @var array
	 */
	protected static $_routes;
	
	/**
	 * 加载路由规则
	 */
	public static function load(){
		if(static::$_routes !== NULL)
			return;
		
		// 加载配置
		$config = Config::load('router');
		// 创建路由规则
		static::$_routes = array();
		foreach ($config as $pattern => $params){
			if(is_int($pattern)) // 普通数组
				$this->_routes[] = new Route($params);
			else // 关联数组
				$this->_routes[] = new Route($pattern, $params);
		}
	}
	
	/**
	 * 解析路由：匹配规则
	 * @param string $uri
	 * @return array|boolean [路由参数, 路由规则]
	 */
	public static function parse($uri){
		// 先尝试加载路由规则
		static::load();
		
		// 逐个匹配路由规则
		foreach ($this->_routes as $route){
			//匹配路由规则	
			$params = $route->match($uri);	
			if($params)
				return array($params, $route);
		}
		
		return FALSE;
	}
	

}