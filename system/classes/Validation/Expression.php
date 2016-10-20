<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 校验表达式
 *    校验表达式是由多个(函数调用的)子表达式组成, 格式为 a(1) & b(1,2) && c(3,4) | d(2) . e(1) > f(5)
 *    子表达式之间用运算符连接, 运算符有 & && | || . >
 *    子表达式是函数调用, 格式为 a(1,2)
 *    
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-19 下午3:40:55  
 *
 */
class Validation_Expression extends Sk_Validation_Expression {}
