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
    private static $instances = array();

    /**
     * 根据不同的配置来获得不同的实例
     * 
     * @param string $config_group
     * @throws Exception
     * @return multitype
     */
    public static function instance($config_group = NULL)
    {
    	// 获得当前类
    	$class = get_called_class();
    	
    	// 默认使用类名小写作为配置组名
    	if($config_group === NULL)
    		$config_group = strtolower($class);
    		
    	if(is_string($config_group))
    		throw new Exception('单例方法 '.$class.'::instance($config_group) 中的参数 $config_group 必须是字符串');
    	
        if (!isset(self::$instances[$config_group])) {
        	// 加载配置项
        	$config = Config::load($config_group);
        	// 根据配置项来创建实例
            self::$instances = new static($config);
        }

        return self::$instances;
    }
}
