<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 控制器
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-8 下午8:02:47 
 *
 */
interface Interface_Controller
{
	/**
	 * 请求对象
	 * @var Request
	 */
// 	public $req;
	
	/**
	 * 响应对象
	 * @var Response
	 */
// 	public $res;
	
	/**
	 * 给默认的视图
	 * 
	 * @param array $data
	 * @return View
	 */
	public function view($data = NULL);
}