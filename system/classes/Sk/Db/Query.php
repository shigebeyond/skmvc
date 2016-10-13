<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql查询器
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query
{
	/**
	 * 数据库连接
	 * @var Db
	 */
	protected $_db;
	
	public function __construct($db)
	{
		// 获得db
		if(!$db instanceof Db)
			$db = Db::instance($db);
		$this->_db = $db;
	}
	
	/**
	 * 执行sql
	 * @param string $sql
	 * @param array $params
	 * @return Db_Result
	 */
	public function execute($sql, array $params = NULL)
	{
		return $this->_db->execute($sql, $params);
	}
	
}