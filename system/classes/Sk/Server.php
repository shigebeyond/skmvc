<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 服务端对象，用于处理请求
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-6 上午9:27:56 
 *
 */
class Sk_Server{
	
	/**
	 * 处理请求
	 * @param Request $req
	 * @param Response $res
	 */
	public static function run()
	{
		// 构建请求与响应对象
		$req = new Request(); 
		$res = new Response();
		
		// 解析路由
		if(!$req->parse_route())
			throw new Exception('当前uri没有匹配路由：'.$req->uri());
		
		// 获得controller类
		$class = $req->controller_class();
		if (!class_exists($class))
			throw new Exception('Controller类不存在：'.$req->controller());
		
		// 创建controller
		$controller = new $class($req, $res);
		
		// 获得action方法
		$action = 'action_'.$req->action();
		if (!method_exists($controller, $action))
			throw new Exception($class.'类不存在方法：'.$action);
		
		// 调用controller的action方法
		$controller->$action();
		
		// 输出响应
		echo $controller->response();
	}
	
}