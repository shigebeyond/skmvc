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
class Sk_Config{
	
	/**
	 * 加载某组配置项
	 * @param unknown $group
	 */
	public static function load($group){
		// TODO: 支持多重路径 + 配置项包装数组 + 加缓存 + 空处理：返回一个空数组
		$config = Loader::load('config', $group);
		return $config;
	}
}