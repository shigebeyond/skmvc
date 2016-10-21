<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 视图
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-21 下午3:14:54  
 *
 */
class Sk_View
{
	/**
	 * 全局变量
	 * @var array
	 */
	protected static $_global_data = array();
	
	/**
	 * 设置全局变量
	 * @param string $key
	 * @param mixed $value
	 * @return View
	 */
	public function set_global($key, $value)
	{
		static::$_global_data[$key] = $value;
		return $this;
	}
	
	/**
	 * 视图文件
	 * @var string
	 */
	protected $_file;
	
	/**
	 * 局部变量
	 * @var array
	 */
	protected $_data = array();
	
	public function __construct($file, $data = NULL)
	{
		$this->_file = $file;
		if($data !== NULL)
			$this->_data = $data;
	}
	
	/**
	 * 设置局部变量
	 * @param string $key
	 * @param mixed $value
	 * @return View
	 */
	public function set($key, $value)
	{
			$this->_data[$key] = $value;
			return $this;
	}
	
	/**
	 * 渲染视图
	 * 
	 * @return string
	 */
	public function render()
	{
		// 释放变量
		extract($this->_data, EXTR_REFS | EXTR_SKIP);
		
		// 开输出缓冲
		ob_start();
		
		// 找到视图
		$view = Loader::find_file('views', $this->_file);
		if(!$view)
			throw new View_Exception("视图文件[$this->_file]不存在");
			
		try {
			// 加载视图, 并输出
			include $view;
			
			// 获得输出缓存
			return ob_get_contents();
		} 
		catch (Exception $e) 
		{
			throw new View_Exception("视图[$this->_file]渲染出错", 500, $e);
		}
		finally 
		{
			// 结束输出缓存
			ob_end_clean();
		}
	}
	
	// 由于php中约定__toString()不能抛出异常, 因此不能调用render()
	/* public function __toString()
	{
		return $this->render();
	} */
}