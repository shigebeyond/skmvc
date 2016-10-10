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
    protected static $_instances = array();

    /**
     * 根据不同的配置来获得不同的实例
     * 
     * @param string $group
     * @throws Exception
     * @return multitype
     */
    public static function instance($name = NULL)
    {
    	// 获得当前类
    	$class = get_called_class();
    	
    	// 使用类名小写作为配置组名，使用 $name 做为路径
    	$group = strtolower($class);
    	if($name !== NULL)
    		$group .= '.'.$name;
    	
    	// 缓存 or 加载
        if (!isset(self::$_instances[$group])) {
        	// 加载配置项
        	$config = Config::load($group);
        	// 根据配置项来创建实例
            self::$_instances = new static($config);
        }

        return self::$_instances;
    }
}
