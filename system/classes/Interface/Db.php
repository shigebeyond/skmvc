<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * 封装数据库连接
 *
 * 		// 获得 config/database.php 中配置的 master 数据库连接
 * 		Db::instance('master')
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-11 上午12:06:58
 *
 */
interface Interface_Db
{
	/**
	 *  获得db单例
	 * @param string $group 数据库配置的分组名
	 * @return Db
	 */
	public static function instance($group = 'default');

	/**
	 * 获得驱动类型
	 * 	从数据库配置项dsn中获取
	 * 	如dsn为mysql:host=localhost;dbname=test， 则driver为mysql
	 * @return
	 */
	public function driver();

	/**
	 * 获得/创建pdo连接
	 * @return PDO
	 */
	public function connect();

	/**
	 * 关闭pdo连接
	 */
	public function disconnect();

	/**
	 * 获得值的pdo类型
	 *
	 * @param unknown $value
	 * @return number
	 */
	public static function pdo_type($value);

	/**
	 * 执行数据变更的sql
	 *
	 * <code>
	 *	  // 插入
	 *    $row_count = $db->execute("INSERT INTO user VALUES (1, 'shi')");
	 *    $row_count = $db->execute("INSERT INTO user VALUES (?, ?)", array(1, 'shi'));
	 * </code>
	 * 
	 * @param string $sql
	 * @param array  $params
	 * @return int 影响行数
	 */
	public function execute($sql, $params = []);

	/**
	 * 执行查询的sql
	 *
	 * <code>
	 *    // 查询
	 *    $resultset = $db->query("SELECT * FROM users WHERE id=1");
	 *    $resultset = $db->query("SELECT * FROM users WHERE id=?", array(1));
	 * </code>
	 * 
	 * @param string $sql
	 * @param array  $params
	 * @param bool|int|string|Orm $fetch_value $fetch_value 如果类型是int，则返回某列FETCH_NUM，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return array
	 */
	public function query($sql, $params = [], $fetch_value = FALSE);

	/**
	 * 根据$pdo->setFetchMode()的第二个参数来确定fetchMode
	 *
	 * @param bool|int|string|Orm $fetch_value $fetch_value 如果类型是int，则返回某列FETCH_NUM，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return number
	 */
	public static function fetch_mode($fetch_value);

	/**
	 * 预览sql
	 *
	 * @param string $sql
	 * @param array $params
	 * @return string
	 */
	public function preview($sql, $params = []);

	/**
	 * 开启事务
	 */
	public function begin();

	/**
	 * 回滚事务
	 */
	public function rollback();

	/**
	 * 提交事务
	 */
	public function commit();

	/**
	 * 获得上一条插入记录的id，即新记录的id
	 * @return int
	 */
	public function last_insert_id();

	/**
	 * 转义表名
	 *
	 * @param string $table
	 * @return string
	 */
	public function quote_table($table, $alias = NULL);

	/**
	 * 转义字段名
	 *
	 * @param string|array $column 字段名, 可以是字段数组
	 * @param string $alias 字段别名
	 * @param bool $with_brackets 当拼接数组时, 是否用()包裹
	 * @return string
	 */
	public function quote_column($column, $alias = NULL, $with_brackets = FALSE);

	/**
	* 转义值
	 *
	 * @param string|array $value 字段值, 可以是值数组
	 * @return string
	 */
	 public function quote($value);

	/**
	 * 查询表的字段
	 * @param string $table
	 * @return array
	 */
	public function list_columns($table);

	/**
	 * select的sql构建器
	 *
	 * @param string $table 表名
	 * @param string $data 数据
	 * @return Sk_Db_Query_Builder
	 */
	public function select($table = NULL, $data = NULL);

	/**
	 * insert的sql构建器
	 *
	 * @param string $table 表名
	 * @param string $data 数据
	 * @return Sk_Db_Query_Builder
	 */
	public function insert($table = NULL, $data = NULL);

	/**
	 * update的sql构建器
	 *
	 * @param string $table 表名
	 * @param string $data 数据
	 * @return Sk_Db_Query_Builder
	 */
	public function update($table = NULL, $data = NULL);

	/**
	 * delete的sql构建器
	 *
	 * @param string $table 表名
	 * @return Sk_Db_Query_Builder
	 */
	public function delete($table = NULL);
	
}
