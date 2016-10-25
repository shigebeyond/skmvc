<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 带配置的组件/读取配置文件的组件
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-6 上午9:27:56 
 *
 */
class Sk_Container_Component_Configurable implements Interface_Container_Component_Configurable
{
	protected $_config;
	
	protected $_name;
	
	public function __construct($config, $name)
	{
		$this->_config = $config;
		$this->_name = $name;
	}
	
	/**
	 * 从容器中删除当前组件
	 */
	public function remove_from_container()
	{
		Container::remove_component($this->_name);
	}
}