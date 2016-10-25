<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 配置读取
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-6 上午12:01:17
 *
 */
interface Interface_Config
{

	/**
	 * 加载某组配置项
	 *
	 *	$config = Config::load('databases');
	 *
	 * @param string $group
	 * @return array
	 */
	public static function load($group, $path = NULL);

	/**
	 * 获得某个路径下的配置数据
	 *
	 * @param string $path
	 * @return array
	 */
	public function get($path = NULL);

}
