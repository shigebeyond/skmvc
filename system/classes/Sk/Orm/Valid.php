<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之数据校验
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-10 上午12:52:34
 *
 */
abstract class Sk_Orm_Valid extends Orm_Entity implements Interface_Orm_Valid
{
	/**
	 * 每个字段的校验规则
	 * @var array
	 */
	protected static $_rules = array();
	
	/**
	 * 每个字段的标签（中文名）
	 * @var array
	*/
	protected static $_labels = array();
	
	/**
	 * 校验数据
	 * @return boolean
	 */
	public function check()
	{
		// 逐个字段校验
		foreach (static::$_rules as $column => $exp)
		{
			$value = $last = $this->$column;
			// 校验单个字段: 字段值可能被修改
			if(!Validation::execute($exp, $value, $this, $message))
			{
				$label = Arr::get(static::$_labels, $column, $column); // 字段标签（中文名）
				throw new Orm_Exception($label.$message);
			}
	
			// 更新被修改的字段值
			if($value !== $last)
				$this->_dirty[$column] = $value;
		}
	
		return TRUE;
	}
}
