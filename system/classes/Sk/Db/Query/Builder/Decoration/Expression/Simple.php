<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 简单表达式
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
class Sk_Db_Query_Builder_Decoration_Expression_Simple extends Db_Query_Builder_Decoration_Expression
{
    /**
     * 添加一个子表达式+连接符
     *
     * @param array $subexp 子表达式
     * @param string $delimiter 当前子表达式的连接符
     * @return Sk_Db_Query_Builder_Expression
     */
    public function add_subexp(array $subexp, $delimiter = ', ')
    {
        // 将连接符也记录到子表达式中, 忽略第一个子表达式的连接符 => 编译好子表达式直接拼接就行
        if(!empty($this->_subexps))
        {
            // 有可能子表达式的size < 元素处理器的size(如limit表达式), 因此以元素处理器的size为准
            $size = count($this->_element_handlers);
            $subexp[$size] = $delimiter;
        }

        $this->_subexps[] = $subexp;
        return $this;
    }

    /**
     * 编译一个子表达式
     * @param array $subexp
     * @return string
     */
    public function compile_subexp($subexp)
    {
        // 遍历处理器来处理对应元素, 没有处理的元素也直接拼接
        foreach ($this->_element_handlers as $i => $handler)
        {
            // 处理某个元素的值
            $value = Arr::get($subexp, $i); // 有可能子表达式的size < 元素处理器的size(如limit表达式)
            $subexp[$i] = $this->{"_$handler"}($value);
        }

        return implode(' ', $subexp); // 用空格拼接多个元素
    }

}