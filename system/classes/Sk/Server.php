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
class Sk_Server implements Interface_Server
{

	/**
	 * 结束输出缓冲
	 */
	public static function ob_end()
	{
		if (function_exists('fastcgi_finish_request')) {
			fastcgi_finish_request();
		} elseif ('cli' !== PHP_SAPI) {
			// ob_get_level() never returns 0 on some Windows configurations, so if
			// the level is the same two times in a row, the loop should be stopped.
			$previous = null;
			$obStatus = ob_get_status(1);
			while (($level = ob_get_level()) > 0 && $level !== $previous) {
				$previous = $level;
				if ($obStatus[$level - 1] && isset($obStatus[$level - 1]['del']) && $obStatus[$level - 1]['del']) {
					ob_end_flush();
				}
			}
			flush();
		}
	}

	/**
	 * 处理请求
	 *
	 * @param Request $req
	 * @param Response $res
	 */
	public static function run()
	{
		// 开始输出缓冲
		ob_start();

		try {
			// 构建请求与响应对象
			$req = new Request();
			$res = new Response();

			// 解析路由
			if(!$req->parse_route())
				throw new Route_Exception('当前uri没有匹配路由：'.$req->uri());

			// 调用路由对应的controller与action
			self::call_controller($req, $res);

			// 输出响应
			$res->send();
		} 
		/* catch (Route_Exception $e) 
		{
			// 输出404响应
			$res->status(404)->send();
		}  */
		catch (Exception $e) 
		{
			echo '异常 - ', $e->getMessage();
		}

		//结束输出缓冲
		//die();
		static::ob_end();
	}

	/**
	 * 调用controller与action
	 *
	 * @param Request $req
	 * @param Response $res
	 */
	private static function call_controller($req, $res)
	 {
		// 获得controller类
		$class = $req->controller_class();
		if (!class_exists($class))
			throw new Route_Exception('Controller类不存在：'.$req->controller());

		// 创建controller
		$controller = new $class($req, $res);

		// 获得action方法
		$action = 'action_'.$req->action();
		if (!method_exists($controller, $action))
			throw new Route_Exception($class.'类不存在方法：'.$action);

		// 调用controller的action方法
		$controller->$action();
	}


}
