<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Odm之元数据
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-29 下午9:09:20 
 *
 */
interface Interface_Odm_MetaData
{
	/**
	 * 获得数据库
	 * 
	 * @param string $action sql动作：select/insert/update/delete，可以用于区分读写的数据库连接
	 * @return Mongoo
	 */
	public static function db($action = 'select');
	
	/**
	 * 获得模型名
	 *    假定model类名, 都是以"Model_"作为前缀
	 * @return string
	 */
	public static function name();
	
	/**
	 * 获得集合名
	 * @return  string
	 */
	public static function collection();
	
	/**
	 * 获得主键值
	 * @return mixed|MongoId
	 */
	public function pk();
}