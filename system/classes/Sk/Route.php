<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 路由规则
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-6 上午12:01:17
 *
 */
class Sk_Route{

	/**
	 * 路由的正则
	 * @var string
	 */
	protected $_pattern;
	
	/**
	 * 路由的预设参数，可以是数组，也可以是映射函数（从匹配结果中获得参数）
	 * @var array|function
	 */
	protected $_params;
	
	/**
	 * 构造函数：设置路由正则
	 * 	TODO 先不做路由正则简化，例子如 '/(?P<controller>.+)\/(?P<action>.+)\/(?P<id>\d+)/'
	 * 
	 * @param string $pattern
	 * @param array $params
	 */
	public function __construct($pattern, $params = array()){
		$this->_pattern = $pattern;
		$this->_params = $params;
	}
	
	/**
	 * 检查uri是否匹配路由正则
	 * 
	 * @param string $uri
	 * @return boolean|array
	 */
	public function match($uri){
		$uri = trim($uri, '/');
		
		if ( ! preg_match($this->_pattern, $uri, $matches))
			return FALSE;
		
		
		$params = $this->_params;
		// 如果预设参数是映射函数，则调用该函数来从匹配结果中获得参数
		if(is_callable($params))
			$params = $params($matches);
		
		//返回 匹配的参数 + 预设的参数
		return array_merge($matches, $params);
	}
}