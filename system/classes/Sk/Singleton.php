<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 单例模式
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-6 下午1:11:11 
 *
 */
class Sk_Singleton
{
    private static $instance = NULL;

    /**
     * 获得单例
     * @return Sk_Singleton
     */
    public static function instance()
    {
        if (self::$instance == NULL) 
            self::$instance = new static;

        return self::$instance;
    }
}
