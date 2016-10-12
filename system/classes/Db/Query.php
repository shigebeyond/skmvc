<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器
 * 	延迟拼接sql, 因为调用方法时元素无序, 但生成sql时元素有序
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
abstract class Db_Query extends Sk_Db_Query {}