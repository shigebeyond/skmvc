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
	 *	 TODO: 支持多重路径 + 配置项包装数组 + 加缓存 + 空处理：返回一个空数组
	 * @param string $group
	 * @return array
	 */
	public static function load($group){
		//先find，后include
		$path = Loader::find('config', $group);

		if(!$path)
			return array();

		return include $path;
	}
}