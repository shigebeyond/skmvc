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
interface Interface_Container_Component_Configurable
{
	/**
	 * 从容器中删除当前组件
	 */
	public function remove_from_container();
}
