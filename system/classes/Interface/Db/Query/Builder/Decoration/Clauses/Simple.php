<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 简单的(sql修饰)子句
 * 
 * 	在子表达式的拼接中，何时拼接连接符？
 *     1 在compile()时拼接连接符 => 你需要单独保存每个子表达式对应的连接符，在拼接时取出
 *     2 在add_subexp()就将连接符也记录到子表达式中 => 在compile()时直接连接子表达式的内容就行，不需要关心连接符的特殊处理
 *     我采用的是第二种
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
interface Interface_Db_Query_Builder_Decoration_Clauses_Simple
{
}