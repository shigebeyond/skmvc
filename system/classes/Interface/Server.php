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
interface Interface_Server
{
	/**
	 * 处理请求
	 *
	 * @param Request $req
	 * @param Response $res
	 */
	public static function run();

}
