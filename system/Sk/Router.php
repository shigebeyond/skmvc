<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 路由器
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-6 上午12:01:17
 *
 */
class Sk_Router{
	
	/**
	 * 路由规则
	 * @var array
	 */
	protected $_routes = [];
	
	public function __construct($config = NULL){
		//TODO:从配置文件中加载路由规则
		if (!$config) {
			
		}
		
		// 创建路由规则
		foreach ($config as $patter => $params){
			$this->_routes[] = new Route($pattern, $params);
		}
	}
	
	/**
	 * 解析路由
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