<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 响应对象
 * 	TODO: 支持响应文件
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-7 下午11:32:07 
 *
 */
interface Interface_Response
{

	/**
	 * 获得与设置响应主体
	 * 
	 * @param string $content
	 * @return string|Sk_Response
	 */
	public function body($content = NULL);
	
	/**
	 * 追加响应主体
	 *
	 * @param string $content
	 * @return string|Sk_Response
	 */
	public function append($content = NULL);
	
	/**
	 * 获得与设置http协议
	 * 
	 * @param string $protocol 协议
	 * @return Sk_Response|string
	 */
	public function protocol($protocol = NULL);
	
	/**
	 * 读取与设置响应状态码
	 * 
	 * @param string $status 状态码
	 * @return number|Sk_Response
	 */
	public function status($status = NULL);
	
	/**
	 * 读取与设置全部头部字段
	 *
	 *       // 获得全部头部字段
	 *       $headers = $response->headers();
	 *
	 *       // 设置头部
	 *       $response->headers(array('Content-Type' => 'text/html', 'Cache-Control' => 'no-cache'));
	 *
	 * @param array $headers 头部字段 
	 * @return mixed
	 */
	public function headers(array $headers = NULL, $merge = TRUE);
	
	/**
	 * 获得与设置单个头部字段
	 * 
	 *       // 获得一个头部字段
	 *       $accept = $response->header('Content-Type');
	 *
	 *       // 设置一个头部字段
	 *       $response->header('Content-Type', 'text/html');
	 * 
	 * @param string $key 字段名
	 * @param string $value 字段值
	 * @return string|Sk_Response
	 */
	public function header($key, $value = NULL);
	
	/**
	 * 设置响应缓存
	 *
	 * @param int|string $expires 过期时间
	 * @return string|Response
	 */
	public function cache($expires = NULL);
	
	/**
	 * 发送头部给客户端
	 * @return Response
	 */
	public function send_headers();
	
	/**
	 * 发送响应该客户端
	 */
	public function send();
}