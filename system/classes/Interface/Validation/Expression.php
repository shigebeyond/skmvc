<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 校验表达式
 *    校验表达式是由多个(函数调用的)子表达式组成, 格式为 a(1) & b(1,2) && c(3,4) | d(2) . e(1) > f(5)
 *    子表达式之间用运算符连接, 运算符有 & && | || . >
 *    子表达式是函数调用, 格式为 a(1,2)
 *    运算符的含义: 
 *        & 与  
 *        && 短路与  
 *        | 或  
 *        || 短路或 
 *         .  字符串连接  
 *         > 累积结果运算
 *   无意于实现完整语义的布尔表达式, 暂时先满足于输入校验与orm保存数据时的校验, 因此: 
 *       运算符没有优先级, 只能按顺序执行, 不支持带括号的子表达式
 *    
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-19 下午3:40:55  
 *
 */
interface Interface_Validation_Expression
{
	/**
	 * 编译 表达式
	 *     表达式是由多个(函数调用的)子表达式组成, 子表达式之间用运算符连接, 运算符有 & && | || . >
	 * 
	 * <code>
	 *     list($ops, $subexps) = Validation_Expression::compile('trim > not_empty && email');
	 * </code>
	 * 
	 * @param string $exp
	 * @return array
	 */
	public static function compile($exp);
	
	/**
	 * 执行校验表达式
	 * 
	 * <code>
	 * 	   // 编译
	 *     $exp = new Validation_Expression('trim > not_empty && email');
	 *     // 执行
	 *     $result = $exp->execute($value, $data, $last_subexp);
	 * </code>
	 * 
	 * @param mixed $value 要校验的数值，该值可能被修改
	 * @param array|ArrayAccess $data 其他参数
	 * @param array $last_subexp 短路时的最后一个子表达式
	 * @return mixed
	 */
	public function execute(&$value, $data = NULL, array &$last_subexp = NULL);
	
}
