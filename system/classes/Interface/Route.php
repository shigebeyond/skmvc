<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 路由规则
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-6 上午12:01:17
 *
 */
interface Interface_Route
{

	/**
	 * 将简化的路由正则，转换为完整的正则：主要是将<参数>替换为子正则
	 *
	 * <code>
	 * 	   // 将 <controller>(\/<action>(\/<id>)?)? 编译为 '/(?P<controller>[a-z]+)(\/(?P<action>[a-z]+)\/(?P<id>\d+)?)?/'
	 *     $compiled = Route::compile(
	 *        '<controller>(\/<action>(\/<id>)?)?',
	 *         array(
	 *           'controller' => '[a-z]+',
	 *           'action' => '[a-z]+',
	 *           'id' => '\d+',
	 *         )
	 *     );
	 * </code>
	 *
	 * @param array $regex 整个uri的正则　
	 * @param array $params 参数的正则
	 * @return  string
	 */
	public static function compile($regex, array $params = NULL);

	/**
	 * 检查uri是否匹配路由正则
	 *
	 * @param string $uri
	 * @return boolean|array
	 */
	public function match($uri);
}
