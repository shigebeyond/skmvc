<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 分组表达式
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
class Sk_Db_Query_Builder_Decoration_Expression_Group extends Db_Query_Builder_Decoration_Expression
{
    /**
     * 开启一个分组
     * @param $delimiter
     * @return Sk_Db_Query_Builder_Decoration_Expression_Group
     */
    public function open($delimiter)
    {
        // 将连接符也记录到子表达式中, 忽略第一个子表达式
        $subexp = '(';
        if (!empty($this->_subexps))
            $subexp = $delimiter.$subexp;

        $this->_subexps[] = $subexp;
        return $this;
    }

    /**
     * 结束一个分组
     * @return Sk_Db_Query_Builder_Decoration_Expression_Group
     */
    public function close()
    {
        $this->_subexps[] = ')';
        return $this;
    }

    /**
     * 获得最后一个子表达式
     * @return Db_Query_Builder_Decoration_Expression
     */
    public function end_subexp()
    {
        $last = end($this->_subexps);
		if(!$last instanceof Sk_Db_Query_Builder_Decoration_Expression_Simple)
            $this->_subexps[] = $last = new Sk_Db_Query_Builder_Decoration_Expression_Simple($this->_db, $this->_element_handlers);
        return $last;
    }

    /**
     * 添加子表达式
     * @param array $subexp
     * @param string $delimiter
     * @return Sk_Db_Query_Builder_Decoration_Expression_Group
     */
    public function add_subexp(array $subexp, $delimiter = ', ')
    {
        // 代理最后一个子表达式
        $this->end_subexp()->add_subexp($subexp, $delimiter);
        return $this;
    }

    /**
     * 编译一个子表达式
     * @param array $subexp
     * @return string
     */
    public function compile_subexp($subexp)
    {
        // 子表达式是: string / Sk_Db_Query_Builder_Decoration_Expression_Simple
        // Sk_Db_Query_Builder_Decoration_Expression_Simple 转字符串自动compile
        return "$subexp";
    }

}