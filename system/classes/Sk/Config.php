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
class Sk_Config extends ArrayObject{
	
	/**
	 * 分组配置的缓存: <分组 => 配置>
	 * @var array
	 */
	protected static $_groups = array();
	
	/**
	 * 加载某组配置项
	 * 
	 *	$config = Config::load('databases');
	 *
	 * @param string $group
	 * @return array
	 */
	public static function load($group, $path = NULL)
	{
		// 分割分组与路径
		if($path === NULL && Text::contains($group, '.'))
			list($group, $path) = explode('.', $group, 2);
		
		// 加载某组配置
		$config = static::_load_group($group);
		
		// 返回某路径下的数据
		return $config->get($path);
	}
	
	/**
	 *  加载某组配置项目
	 *  
	 * @param string $group
	 * @return array
	 */
	protected static function _load_group($group)
	{
		// 先查缓存, 后加载
		if(!isset(static::$_groups[$group]))
		{
			$data = Loader::load_file('config', $group, array());
			static::$_groups[$group] = new Config($data);
		}
		
		return static::$_groups[$group];
	}
	
	/**
	 * 获得某个路径下的配置数据
	 * 
	 * @param string $path
	 * @return array
	 */
	public function get($path = NULL)
	{
		return $path ? Arr::path($this, $path) : $this;
	}
}