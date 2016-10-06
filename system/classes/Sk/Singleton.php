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
    private static $instance = array();

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new static;
        }

        return self::$instance;
    }
}
