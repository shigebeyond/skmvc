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
	 * 路由规则
	 * @var array
	 */
	protected $_routes = [];
	
	public function __construct($config){
		// 创建路由规则
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
	 * @return array|boolean
	 */
	public function parse($uri){
		// 逐个匹配路由规则
		foreach ($this->_routes as $route){
			//匹配路由规则	
			$params = $route->matche($uri);	
			if($params)
				return $params;
		}
		
		return FALSE;
	}
}