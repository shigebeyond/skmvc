<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * join表达式
 * 	由于join的语法比较特别，是故特殊处理
 *  1 on： 直接继承Db_Query_Builder_Decoration_Expression_Group
 *  2 join：单独处理表名+联表类型
 *  
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
class Db_Query_Builder_Decoration_Expression_Join extends Sk_Db_Query_Builder_Decoration_Expression_Join {}