<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 请求对象
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-6 上午9:27:56 
 *
 */
class Sk_Request{
	
	/**
	 * base url
	 * @var  string 
	 */
	public static $base_url = '/';
	
	/**
	 * 入口文件
	 * @var  string
	 */
	public static $index_file = 'index.php';
	
	/**
	 * 从$_SERVER中解析出相对路径
	 * @return string
	 */
	public static function parse_uri(){
		// 1 如果重写了url，则直接使用PATH_INFO，不含index.php与query string
		if ( ! empty($_SERVER['PATH_INFO']))
		{
			return $_SERVER['PATH_INFO'];
		}
	
		// 2 使用REQUEST_URI
		$uri = $_SERVER['REQUEST_URI'];
	
		// 去掉base url与入口文件部分
		$pref = [static::$base_url.static::$index_file, static::$base_url]; // 匹配顺序：先长后短
		return str_replace($pref, "", $uri);
	}
	
	/**
	 * 当前uri
	 * @var string
	 */
	protected $_uri;
	
	/**
	 * 当前匹配的路由规则
	 * @var Route
	 */
	protected $_route;
	
	/**
	 * 当前匹配的路由惨素
	 * @var array
	 */
	protected $_params = array();
	
	public function __construct($uri = NULL){
		if($uri === NULL)
			$uri = static::parse_uri();
		
		$this->_uri = $uri;
	}

	/**
	 * 获得当前uri
	 */
	public function uri()
	{
		return $this->_uri;
	}
	

	/**
	 * 获得当前匹配的路由规则
	 */
	public function route()
	{
		return $this->_route;
	}
	
	/**
	 * 获得当前匹配路由的所有参数/单个参数
	 * 
	 * @param string $key 如果是null，则返回所有参数，否则，返回该key对应的单个参数
	 * @param string $default 单个参数的默认值
	 * @return multitype
	 */
	public function param($key = NULL, $default = NULL)
	{
		if ($key === NULL)
			return $this->_params;

		return isset($this->_params[$key]) ? $this->_params[$key] : $default;
	}
	
	/**
	 * 获得当前controller
	 */
	public function controller()
	{
		return $this->_params['controller'];
	}
	
	/**
	 * 获得当前action
	 */
	public function action()
	{
		return $this->_params['action'];
	}
	
	/**
	 * 解析路由
	 * @return boolean
	 */
	public function parse_route(){
		// 路由解析
		list($params, $route) = Router::instance()->parse($this->_uri);
		
		// 设置匹配的路由参数
		if($params){
			$this->_params = $params;
			$this->_route = $route;
			return TRUE;
		}
		
		return FALSE;
	}
}