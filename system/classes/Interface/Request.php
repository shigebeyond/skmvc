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
interface Interface_Request
{
	/**
	 * 从$_SERVER中解析出相对路径
	 * @return string
	 */
	public static function prepare_uri();

	/**
	 * 解析路由
	 * @return bool
	 */
	public function parse_route();

	/**
	 * 获得当前uri
	 */
	public function uri();

	/**
	 * 获得当前匹配的路由规则
	 * @return Route
	 */
	public function route();

	/**
	 * 获得当前匹配路由的所有参数/单个参数
	 *
	 * @param string $key 如果是null，则返回所有参数，否则，返回该key对应的单个参数
	 * @param string $default 单个参数的默认值
	 * @param   string $filter  参数过滤表达式, 如 "trim > htmlspecialchars"
	 * @return multitype
	 */
	public function param($key = NULL, $default = NULL, $filter_exp = NULL);

	/**
	 * 获得当前目录
	 * @return string
	 */
	public function directory();

	/**
	 * 获得当前controller
	 * @return string
	 */
	public function controller();

	/**
	 * 获得当前controller的类名
	 * @return string
	 */
	public function controller_class();

	/**
	 * 获得当前action
	 * @return string
	 */
	public function action();

	/**
	 * 获得get参数
	 *
	 * @param   string $key    参数名
	 * @param   string $default  参数默认值
	 * @param   string $filter  参数过滤表达式, 如 "trim > htmlspecialchars"
	 * @return  mixed
	 */
	public function get($key = NULL, $default = NULL, $filter_exp = NULL);

	/**
	 * 获得post参数
	 *
	 * @param   string $key    参数名
	 * @param   string $default  参数默认值
	 * @param   string $filter  参数过滤表达式, 如 "trim > htmlspecialchars"
	 * @return  mixed
	 */
	public function post($key = NULL, $default = NULL, $filter_exp = NULL);
	
	/**
	 * 请求方法
	 * @return string
	 */
	public function method();

	/**
	 * 是否post请求
	 * @return bool
	 */
	public function is_post();

	/**
	 * 是否get请求
	 * @return bool
	 */
	public function is_get();

	/**
	 * 是否ajax请求
	 * @return boolean
	 */
	public function is_ajax();

	/**
	 * 是否是https请求
	 * @return boolean
	 */
	public function is_https();

	/**
	 * 获得协议
	 * @return string
	 */
	public function scheme();

	/**
	 * 获得post的原始数据
	 * @return string
	 */
	public function body();

	/**
	 * 获得cookie
	 * @param string $key
	 * @param string $default
	 */
	public function cookie($key, $default = NULL);

	/**
	 * 客户端要接受的数据类型
	 * @return string
	 */
	public function accept_types();

	/**
	 * 获得客户端ip
	 * @return string
	 */
	public function client_ip();

	/**
	 * 获得user agent
	 * @return string
	 */
	public function user_agent();
	
}
