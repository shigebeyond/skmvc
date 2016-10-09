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
	 * 参数名的正则
	 * @var string
	 */
	const REGEX_NAME = '/\<(\w+)\>/';
	
	/**
	 * 参数的默认正则
	 * @var string
	 */
	const REGEX_PARAM = '[^\/]+';

	/**
	 * 原始正则: <controller>(\/<action>(\/<id>)?)?
	 * @var string
	 */
	protected $_regex;
	
	/**
	 * 编译后正则: /(?P<controller>[^\/]+)(\/(?P<action>[^\/]+)\/(?P<id>\d+)?)?/
	 * @var string
	 */
	protected $_compile_regex;
	
	/**
	 * 参数的子正则
	 * @var array
	 */
	protected $_params;
	
	/**
	 * 参数的默认值
	 * @var array
	 */
	protected $_defaults;
	
	/**
	 * 构造函数：设置路由正则
	 * 
	 * @param string $regex uri的正则
	 * @param array $param 参数的子正则
	 * @param array $default 参数的默认值
	 */
	public function __construct($regex, array $params = NULL, array $default = NULL){
		$this->_regex = $regex;
		$this->_params = $params;
		$this->_defaults = $default;
		
		//编译简化的路由正则
		$this->_compile_regex = static::compile($regex, $params);
		
		echo "regex = $regex, compile_regex = {$this->_compile_regex}";
	}
	
	/**
	 * 将简化的路由正则，转换为完整的正则：主要是将参数替换为子正则
	 *
	 *     $compiled = Route::compile(
	 *        '<controller>(\/<action>(\/<id>)?)?',
	 *         array(
	 *           'controller' => '[a-z]+',
	 *           'action' => '[a-z]+',
	 *           'id' => '\d+',
	 *         )
	 *     );
	 *
	 * => 将 <controller>(\/<action>(\/<id>)?)? 编译为 '/(?P<controller>[a-z]+)(\/(?P<action>[a-z]+)\/(?P<id>\d+)?)?/'
	 * 
	 * @param array $regex 整个uri的正则　
	 * @param array $params 参数的正则
	 * @return  string
	 */
	public static function compile($regex, array $params = NULL)
	{
		// 将<参数>替换为对应的带参数的子正则，如将<controller>替换为(?P<controller>[^\/]+)
		$regex = preg_replace_callback(static::REGEX_NAME, function($matches) use($params){
			return static::compile_param($matches[1], $params);
		}, $regex);
		
		// 匹配开头与结尾
		return "/^$regex$/";
	}
	
	/**
	 *　编译单个参数的正则
	 *
	 * @param string $name 参数名
	 * @param array $params 参数的正则
	 * @return string 带参数的子正则
	 */
	public function compile_param($name, array $params = NULL){
		$regex = Arr::get($params, $name, static::REGEX_PARAM); //　参数的正则
		return "(?P<$name>$regex)"; // 带参数的子正则
	}
	
	/**
	 * 检查uri是否匹配路由正则
	 * 
	 * @param string $uri
	 * @return boolean|array
	 */
	public function match($uri){
		// 去掉两头的/
		$uri = trim($uri, '/');
		
		// 匹配uri
		if ( ! preg_match($this->_compile_regex, $uri, $matches))
			return FALSE;
		
		//返回 默认参数 + 匹配的参数
		return array_merge($this->_defaults, $matches);
	}
}