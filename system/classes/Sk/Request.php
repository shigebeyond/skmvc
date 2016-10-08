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
	 * 当前请求对象
	 * @var  Request
	 */
	public static $current;
	
	/**
	 * 可信任的代理服务器ip
	 * @var array
	 */
	public static $proxy_ips = array('127.0.0.1', 'localhost', 'localhost.localdomain');
	
	/**
	 * 获得当前请求对象
	 * @return Request
	 */
	public static function current()
	{
		return static::$current;
	}
	
	/**
	 * 从$_SERVER中解析出相对路径
	 * @return string
	 */
	public static function prepare_uri(){
		// 1 如果重写了url，则直接使用PATH_INFO，不含index.php与query string
		if ( ! empty($_SERVER['PATH_INFO']))
			return $_SERVER['PATH_INFO'];
		
		// 2 使用REQUEST_URI，不含query string
		$uri = $_SERVER['REQUEST_URI'];
		
		// 去掉base url与入口文件部分
		$config = Config::load('sk');
		$pref = [$config['base_url'].$config['index_file'], $config['base_url']]; // 匹配顺序：先长后短
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
	 * 当前匹配的路由参数
	 * @var array
	 */
	protected $_params = array();
	
	/**
	 * post的原始数据
	 * @var string
	 */
	protected $_body;
	
	/**
	 * 客户端ip
	 * @var string
	 */
	protected $_client_ip;
	
	
	public function __construct($uri = NULL){
		$this->_uri = $uri;
		static::$current = $this;
	}
	
	/**
	 * 解析路由
	 * @return bool
	 */
	public function parse_route(){
		// 解析路由
		list($params, $route) = Router::parse($this->uri());
		
		if($params){
			$this->_params = $params;
			$this->_route = $route;
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * 获得当前uri
	 */
	public function uri()
	{
		if($this->_uri === NULL)
			$this->_uri = static::prepare_uri();
		
		return $this->_uri;
	}

	/**
	 * 获得当前匹配的路由规则
	 * @return Route
	 */
	public function route(){
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
		// 全部参数
		if ($key === NULL)
			return $this->_params;
	
		// 单个参数
		return Arr::get($this->_params, $key, $default);
	}
	
	/**
	 * 获得当前目录
	 * @return string
	 */
	public function directory()
	{
		return $this->param('directory');
	}
	
	/**
	 * 获得当前controller
	 * @return string
	 */
	public function controller()
	{
		return $this->param('controller');
	}
	
	/**
	 * 获得当前controller的类名
	 * @return string
	 */
	public function controller_class()
	{
		// 类前缀
		$class = 'Controller_';
		
		// 目录
		if($this->directory())
			$class .= Text::ucfirst($this->directory());
		
		// controller
		return $class.Text::ucfirst($this->controller());
	}
	
	/**
	 * 获得当前action
	 * @return string
	 */
	public function action()
	{
		return $this->param('action');
	}

	/**
	 * 获得get参数
	 *
	 * @param   string  $key    参数名
	 * @param   string  $value  参数默认值
	 * @return  mixed
	 */
	public function query($key = NULL, $value = NULL)
	{
		// 获得全部参数
		if ($key === NULL)
			return $_GET;
		
		// 获得单个参数
		return Arr::path($_GET, $key);
	}
	
	/**
	 * 获得post参数
	 *
	 * @param   string $key    参数名
	 * @param   string $value  参数默认值
	 * @return  mixed
	 */
	public function post($key = NULL, $value = NULL)
	{
		// 获得全部参数
		if ($key === NULL)
			return $_POST;
		
		// 获得单个参数
		return Arr::path($_POST, $key);
	}
	
	/**
	 * 请求方法
	 * @return string
	 */
	public function method(){
		return $_SERVER['REQUEST_METHOD'];
	}
	
	/**
	 * 是否post请求
	 * @return bool
	 */
	public function is_post()
	{
		return $this->method() === 'POST';
	}
	
	/**
	 * 是否get请求
	 * @return bool
	 */
	public function is_get()
	{
		return $this->method() === 'GET';
	}
	
	/**
	 * 是否ajax请求
	 * @return boolean
	 */
	public function is_ajax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' // 通过XMLHttpRequest发送请求
				|| $this->accept_types() == 'text/javascript, application/javascript, */*'; // 通过jsonp来发送请求
	}
	
	/**
	 * 是否是https请求
	 * @return boolean
	 */
	public function is_https()
	{
		return isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'off'); // 忽略大小写的比较，相等为0，大于为1，小于为-1
	}
	
	/**
	 * 获得协议
	 * @return string
	 */
	public function scheme()
	{
		if (isset($_SERVER['REQUEST_SCHEME'])) 
			return $_SERVER['REQUEST_SCHEME'];
		
		return $this->is_https() ? 'https' : 'http';
	}
	
	/**
	 * 获得post的原始数据
	 * @return string
	 */
	public function body(){
		if($this->isGet())
			return NULL;
		
		if($this->_body === NULL)
			$this->_body = file_get_contents('php://input');
		
		return $this->_body;
	}
	
	/**
	 * 获得cookie
	 * @param string $key
	 * @param string $default
	 */
	public static function get($key, $default = NULL){
		return Cookie::get($key, $default);
	}
	
	/**
	 * 客户端要接受的数据类型
	 * @return string
	 */
	public function accept_types()
	{
		return Arr::get($_SERVER, 'HTTP_ACCEPT');
	}
	
	/**
	 * 获得客户端ip
	 * @return string
	 */
	public function client_ip()
	{
		// 读缓存
		if($this->_client_ip !== NULL)
			return $this->_client_ip;
		
		// 未知ip
		if(!isset($_SERVER['REMOTE_ADDR']))
			return $this->_client_ip = '0.0.0.0';
		
		// 客户端走代理
		if(in_array($_SERVER['REMOTE_ADDR'], static::$proxy_ips)){
			foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP') as $header){
				// Use the forwarded IP address, typically set when the
				// client is using a proxy server.
				// Format: "X-Forwarded-For: client1, proxy1, proxy2"
				if(isset($_SERVER[$header]))
					return $this->_client_ip = strstr($_SERVER[$header], ',', true);
			}
		}
		
		// 客户端没走代理
		return $this->_client_ip = $_SERVER['REMOTE_ADDR'];
	}
	
	/**
	 * 获得user agent
	 * @return string
	 */
	public function user_agent()
	{
		return Arr::get($_SERVER, 'HTTP_USER_AGENT');
	}
}