<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 打日志
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-11-3 下午8:01:38  
 *
 */
class Sk_Log extends Container_Component_Configurable
{
	/**
	 * 日志级别
	 * @var array
	 */
	const LEVELS	= array('OFF' => 6, 'ERROR' => 5, 'WARN' => 4, 'INFO' => 3, 'DEBUG' => 2, 'ALL' => 1);
	
	/**
	 *  获得日志单例
	 * @param string $group 日志配置的分组名
	 * @return Db
	 */
	public static function instance($group = 'default')
	{
		return Container::component_config('Log', $group);
	}
	
	public function __construct($config, $name)
	{
		parent::__construct($config, $name);
		
		// 检查目录
		if(!is_dir(LOGPATH))
			throw new Sk_Exception('日志目录不存在: '.LOGPATH);
		
		if(!is_writable(LOGPATH))
			throw new Sk_Exception('日志目录不可写: '.LOGPATH);
	}
	
	/**
	 * 写日志
	 * 
	 * @param string $level 日志级别
	 * @param string $msg　消息
	 * @return boolean
	 */
	public function add($level, $msg)
	{
		// 不够最低级别: 不写日志
		$level = strtoupper($level);
		if(Arr::get(static::LEVELS, $level, 7) < $this->_config['threshold'])
			return FALSE;

		// 创建并打开文件
		$file = LOGPATH.str_replace(':date', date('Y-m-d'), $this->_config['file']);
		if (!$fp = fopen($file, 'ab'))
			return FALSE;

		// 准备日志参数
		$params = array(
			':level' => $level,
			':date' => date($this->_config['date_format']),
			':uri' => $this->uri(),
			':msg' => $msg,
		);
		
		// 格式化日志
		$content = strtr($this->_config['log_format'], $params);

		// 写日志
		flock($fp, LOCK_EX);
		fwrite($fp, $content);
		flock($fp, LOCK_UN);
		fclose($fp);

		chmod($file, 0666);
		return TRUE;
	}
	
	/**
	 * 　获得当前uri
	 */
	 public function uri() 
	 {
		$uri = Request::$current->uri();
		if($_SERVER['QUERY_STRING'])
			$uri .= '?'.$_SERVER['QUERY_STRING'];
		
		return $uri;
	}


	/**
	 * 写错误日志
	 *
	 * @param string $msg　消息
	 * @return boolean
	 */
	public static function error($msg)
	{
		return static::instance()->add('ERROR', $msg);
	}

	/**
	 * 写警告日志
	 *
	 * @param string $msg　消息
	 * @return boolean
	 */
	public static function warn($msg)
	{
		return static::instance()->add('WARN', $msg);
	}

	/**
	 * 写通知日志
	 *
	 * @param string $msg　消息
	 * @return boolean
	 */
	public static function info($msg)
	{
		return static::instance()->add('INFO', $msg);
	}

	/**
	 * 写调试日志
	 *
	 * @param string $msg　消息
	 * @return boolean
	 */
	public static function debug($msg)
	{
		return static::instance()->add('DEBUG', $msg);
	}
	
	/**
	 * 写所有日志
	 *
	 * @param string $msg　消息
	 * @return boolean
	 */
	public static function all($msg)
	{
		return static::instance()->add('ALL', $msg);
	}
}
