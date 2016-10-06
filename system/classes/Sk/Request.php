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
	protected $_uri = FALSE;

	/**
	 * 获得当前uri
	 */
	public function uri()
	{
		if($this->_uri === FALSE)
			$this->_uri = static::parse_uri();
		
		return $this->_uri;
	}
	
	public function execute(){
		
	}
}