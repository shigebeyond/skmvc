<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 面向orm对象的sql构建器
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-16 下午8:02:28 
 *
 */
class Sk_Orm_Query_Builder extends Db_Query_Builder 
{
	/**
	 * model的类
	 * @var string
	 */
	protected $_class;
	
	/**
	 * 构造函数
	 * @param string $action sql动作：select/insert/update/delete
	 * @param string $class model类名，其基类为Orm
	 * @param array $data 数据
	 */
	public function __construct($action, $class, $data = NULL)
	{
		parent::__construct($action, $class::db($action), $class::table(), $data);
		$this->_class = $class;
	}
	
	/**
	 * 查询单个
	 * @return 
	 */
    public static function find()
    {
    	return $this->limit(1)->execute($this->_class);
    }
	
    /**
     * 查询多个
     * @return array
     */
    final public static function findAll()
    {
    	return $this->execute($this->_class);
    }
}
