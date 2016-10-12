<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 可配置的单例模式
 * 	一种配置有一个实例
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-6 下午1:11:11 
 *
 */
class Sk_Singleton_Configurable
{
	/**
	 * 实例池: <$name, 实例>
	 * @var array
	 */
	protected static $_instances = array ();
	
	/**
	 * 根据不同的配置来获得不同的实例
	 *
	 * @param string $name	分类名, 可作为配置中的子路径
	 * @param array $config 配置信息，默认为NULL，表示从配置文件中加载，但也可以手动指定
	 * @throws Exception
	 * @return multitype
	 */
	public static function instance($name = 'default', array $config = NULL) 
	{
		// 缓存 or 加载
		if (! isset ( self::$_instances [$name] )) 
		{
			// 从配置文件中加载配置信息
			if($config === NULL)
				$config = self::load_config ( $name );

			// 获得子类后缀
			$subclass_suffix = static::subclass_suffix ( $config );
			// 获得子类 = 父类_子类后缀
			if ($subclass_suffix)
				$class .= '_' . $subclass_suffix;
				
				// 根据配置项来创建实例
			self::$_instances = new $class ( $config, $name );
		}
		
		return self::$_instances;
	}
	
	/**
	 * 根据分类名 自动加载配置信息
	 * 
	 * @param name
	 * @return array
	 */
	 public static function load_config($name) 
	 {
		// 获得当前类
		$class = get_called_class ();
		
		// 使用类名小写作为配置组名
		$group = strtolower ( $class );
		// 使用 $name 作为配置路径
		if ($name)
			$group .= '.' . $name;
		
		// 加载配置项
		return Config::load ( $group );
	}

	
	/**
	 * 根据配置来获得子类后缀
	 *
	 * @param array $config        	
	 * @return string
	 */
	public static function subclass_suffix($config) 
	{
		return NULL;
	}
	
	/**
	 * 分类名
	 * @var string
	 */
	protected $_name;
	
	/**
	 * 配置
	 * @var array
	 */
	protected $_config;
	
	/**
	 * 强制子类要处理接收到的两个参数
	 *
	 * @param array $config 对应的配置项
	 * @param string $name	分类名
	 */
	public function __construct($config, $name = NULL) 
	{
		$this->_config = $config;
		$this->_name = $name;
	}
}
