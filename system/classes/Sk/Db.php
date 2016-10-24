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
class Sk_Db extends Container_Component_Configurable
{
	/**
	 * 查询表字段的sql/字段名
	 * @var array
	 */
	public static $columns_sql = array(
		'mysql' => array('DESC :table', 'Field'), // mysql
		'sqlsrv' => array("SELECT * FROM INFORMATION_SCHEMA.columns WHERE TABLE_NAME=':table'", 'COLUMN_NAME'), // sql server
		'oci' => array("SELECT * FROM user_tab_columns WHERE Table_Name=':table'", 'column_name'), // oracle
	);

	/**
	 *  获得db单例
	 * @param string $group 数据库配置的分组名
	 * @return Db
	 */
	public static function instance($group = 'default')
	{
		return Container::component_config('Db', $group);
	}

	/**
	 * 当前事务的嵌套层级
	 * @var int
	 */
	protected $_trans_level = 0;

	/**
	 * pdo连接
	 * @var PDO
	 */
	protected $_pdo;

	public function __construct($config, $name)
	{
		parent::__construct($config, $name);
		// 创建pdo连接
		$this->connect();
	}

	/**
	 * 获得驱动类型
	 * 	从数据库配置项dsn中获取
	 * 	如dsn为mysql:host=localhost;dbname=test， 则driver为mysql
	 * @return
	 */
	public function driver()
	{
		$dsn = $this->_config['dsn'];
		$i = strpos($dsn, ':');
		if($i !== FALSE)
			return substr($dsn, 0, $i);

		return NULL;
	}

	/**
	 * 获得/创建pdo连接
	 * @return PDO
	 */
	public function connect()
	{
		if ($this->_pdo)
			return;

		// 获得pdo选项
		$options = Arr::get($this->_config, 'options', array());
		$options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION; // 错误处理: 出错就跑出异常
		$options[PDO::ATTR_PERSISTENT] = Arr::get($this->_config, 'persistent', FALSE); // 保持连接

		try
		{
			// 创建pdo连接
			$this->_pdo = new PDO($this->_config['dsn'], $this->_config['username'], $this->_config['password'], $options);
		}
		catch (PDOException $e)
		{
			throw new Db_Exception("连接失败: " . $e->getMessage());
		}

		// 设置字符集
		if (!empty($this->_config['charset']))
			$this->_pdo->exec('SET NAMES '.$this->quote($this->_config['charset']));
	}

	/**
	 * 关闭pdo连接
	 */
	public function __destruct()
	{
		$this->_pdo = NULL;
		$this->remove_from_container();
	}


	/**
	 * 获得值的pdo类型
	 *
	 * @param unknown $value
	 * @return number
	 */
	public static function pdo_type($value)
	{
		if(is_int($value))
			return PDO::PARAM_INT;

		if(is_bool($value))
			return PDO::PARAM_BOOL;

		if(is_null($value))
			return PDO::PARAM_NULL;

		if(is_string($value))
			return PDO::PARAM_STR;

		return FALSE;
	}

	/**
	 * 执行sql
	 *
	 * @param sql $sql
	 * @param array $params
	 * @param string $query
	 * @return PDOStatement
	 */
	protected function _exec($sql, $params)
	{
		// 1 解析sql
		$statement = $this->_pdo->prepare($sql);

		// 2 绑定参数
		// execute(多个参数): 不能指定参数类型
		// $statement->execute($params);

		// bindValue(参数)： 可以指定参数类型
		foreach ($params as $key => $value) {
			// $key 类型： 1 int 参数样式为 ?, 则使用从1开始的数 2 string 参数样式为 :name, 则直接使用
			$key = is_int($key) ? $key + 1 : $key;

			// 绑定参数
			$statement->bindValue($key, $value, static::pdo_type($value));
		}

		// 3 执行
		$statement->execute();
		return $statement;
	}

	/**
	 * 执行数据变更的sql
	 *
	 *	  // 插入
	 *    $row_count = $db->execute("INSERT INTO user VALUES (1, 'shi')");
	 *    $row_count = $db->execute("INSERT INTO user VALUES (?, ?)", array(1, 'shi'));
	 *
	 * @param string $sql
	 * @param array  $params
	 * @return int 影响行数
	 */
	public function execute($sql, $params = [])
	{
		try {
			// 执行sql + 返回影响行数
			return $this->_exec($sql, $params)->rowCount();
		} catch (PDOException $e) {
			throw new Db_Exception("执行sql出错: ", $e->getMessage());
		}
	}

	/**
	 * 执行查询的sql
	 *
	 *    // 查询
	 *    $resultset = $db->query("SELECT * FROM users WHERE id=1");
	 *    $resultset = $db->query("SELECT * FROM users WHERE id=?", array(1));
	 *
	 * @param string $sql
	 * @param array  $params
	 * @param bool|int|string|Orm $fetch_value $fetch_value 如果类型是int，则返回某列FETCH_NUM，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return array
	 */
	public function query($sql, $params = [], $fetch_value = FALSE)
	{
		try {
			// 执行sql
			$statement = $this->_exec($sql, $params);
			// 直接查出所有数据, 因为PDOStatement::rowCount是不可靠的
			if(!$fetch_value)
				return $statement->fetchAll(PDO::FETCH_ASSOC); // fix bug: General error: Extraneous additional parameters => 不需要第二个参数
			return $statement->fetchAll(static::fetch_mode($fetch_value), $fetch_value);
		} catch (PDOException $e) {
			throw new Db_Exception("执行sql出错: ", $e->getMessage());
		}
	}

	/**
	 * 根据$pdo->setFetchMode()的第二个参数来确定fetchMode
	 *
	 * @param bool|int|string|Orm $fetch_value $fetch_value 如果类型是int，则返回某列FETCH_NUM，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return number
	 */
	public static function fetch_mode($fetch_value)
	{
		if(is_int($fetch_value))
			return PDO::FETCH_NUM;

		if(is_callable($fetch_value)) // 优先于string/object, 因为函数名是string, 匿名函数Closure是object
			return PDO::FETCH_FUNC;

		if(is_string($fetch_value))
			return PDO::FETCH_CLASS;

		if(is_object($fetch_value))
			return PDO::FETCH_INTO;

		return PDO::FETCH_ASSOC;
	}

	/**
	 * 预览sql
	 *
	 * @param string $sql
	 * @param array $params
	 * @return string
	 */
	public function preview($sql, $params = [])
	{
		static $i;
		$i = 0;
		return preg_replace_callback(
				array('/\?/', '/:\w+/'),
				function($matches) use(&$i, $params){
					// 获得参数标识
					$key = $matches[0]; // 参数样式为 :name，则直接使用
					if($key == '?') // 参数样式为 ?, 则为数字
						$key = $i++;

					// 若不存在该参数，则不转换
					if (!isset($params[$key]))
						return $matches[0];

					// 否则，转换
					return $this->quote($params[$key]);
				},
				$sql
		);
	}

	/**
	 * 开启事务
	 */
	public function begin()
	{
		if($this->_trans_level++ === 0)
			return $this->_pdo->beginTransaction();

		return TRUE;
	}

	/**
	 * 结束事务
	 *
	 * @param bool $commited 提交 or 回滚
	 * @return boolean
	 */
	protected function _end($commited)
	{
		// 未开启事务
		if ($this->_trans_level === 0)
			return FALSE;

		// 无嵌套事务
		if (--$this->_trans_level === 0)
			return $commited ?  $this->_pdo->commit() : $this->_pdo->rollBack(); // 提交 or 回滚事务

		// 有嵌套事务
		return TRUE;
	}

	/**
	 * 回滚事务
	 */
	public function rollback()
	{
		return $this->_end(FALSE);
	}

	/**
	 * 提交事务
	 */
	public function commit()
	{
		return $this->_end(TRUE);
	}

	/**
	 * 获得上一条插入记录的id，即新记录的id
	 * @return int
	 */
	public function last_insert_id()
	{
		return (int)$this->_pdo->lastInsertId();
	}

	/**
	 * 转义多个表名
	 *
	 * @param string|array $column 表名, 可以是表数组: <alias, column>
	 * @return string
	 */
	protected function _quote_tables($tables, $with_brackets = FALSE)
	{
		// 遍历多个表转义
		$str = '';
		foreach ($tables as $alias => $column)
		{
			if (is_int($alias)) // 无别名
				$alias = NULL;
			// 单个表转义
			$str .= $this->quote_table($column, $alias).', ';
		}
		$str = rtrim($str, ', ');
		return $with_brackets ? "($str)" : $str;
	}

	/**
	 * 转义表名
	 *
	 * @param string $table
	 * @return string
	 */
	public function quote_table($table, $alias = NULL)
	{
		// 数组: 逐个处理
		if(is_array($table))
			return $this->_quote_tables($table);

		// 表名
		$table = "`$table`"; // 转义

		// 表别名
		if($alias)
			$alias = " AS `$alias`"; // 转义

		return $this->_config['table_prefix'].$table.$alias; // 表前缀 + 表名 + 表别名
	}

	/**
	 * 转义多个字段名
	 *
	 * @param string|array $column 字段名, 可以是字段数组: <alias, column>
	 * @param bool $with_brackets 当拼接数组时, 是否用()包裹
	 * @return string
	 */
	protected function _quote_columns($columns, $with_brackets = FALSE)
	{
		//$str = array_map(array($this, 'quote_column'), $columns);
		// 遍历多个字段转义
		$str = '';
		foreach ($columns as $alias => $column)
		{
			if (is_int($alias)) // 无别名
				$alias = NULL;
			// 单个字段转义
			$str .= $this->quote_column($column, $alias).', ';
		}
		$str = rtrim($str, ', ');
		return $with_brackets ? "($str)" : $str;
	}

	/**
	 * 转义字段名
	 *
	 * @param string|array $column 字段名, 可以是字段数组
	 * @param string $alias 字段别名
	 * @param bool $with_brackets 当拼接数组时, 是否用()包裹
	 * @return string
	 */
	public function quote_column($column, $alias = NULL, $with_brackets = FALSE)
	{
		// 数组: 逐个处理
		if(is_array($column))
			return $this->_quote_columns($column, $with_brackets);

		// 表名
		$table = NULL;
		$parts = explode('.', $column, 2); //分离"表名.字段名"
		if(isset($parts[1]))
		{
			list($table, $column) = $parts;
			$table = "`$table`.";
		}

		// 字段名
		if($column != '*') // 非*
			$column = "`$column`"; // 转义

		// 字段别名
		if($alias)
			$alias = " AS `$alias`"; // 转义

		return $table.$column.$alias;
	}

	/**
	* 转义值
	 *
	 * @param string|array $value 字段值, 可以是值数组
	 * @return string
	 */
	 public function quote($value)
	 {
	 	// 数组: 逐个处理
		if(is_array($value))
		{
			$value = array_map(array($this, 'quote'), $value );
			// 头部 + 连接符拼接多值 + 尾部
			return '('.implode(', ', $value).')';
		}

		// NULL => 'NULL'
		if($value === NULL)
			return 'NULL';

		// bool => int
		if(is_bool($value))
			return (int)$value;

		// int/float
		if(is_int($value) || is_float($value))
			return $value;

		// 非string => string
		if(!is_string($value))
			$value = "$value";

		// 转义
		return $this->_pdo->quote ( $value );
	}

	/**
	 * 查询表的字段
	 * @param string $table
	 * @return array
	 */
	public function list_columns($table)
	{
		// 查询
		list($sql, $field) = static::$columns_sql[$this->driver()];
		$sql = str_replace(':table', $table, $sql);
		$rows = $this->query($sql);
		// 构建返回数组: <字段名 => 行>
		$columns = array();
		foreach ($rows as $row)
			$columns[$row[$field]] = $row;
		return $columns;
	}

	/**
	 * select的sql构建器
	 *
	 * @param string $table 表名
	 * @param string $data 数据
	 * @return Sk_Db_Query_Builder
	 */
	public function select($table = NULL, $data = NULL)
	{
		return new Sk_Db_Query_Builder ( 'select', $this, $table, $data );
	}

	/**
	 * insert的sql构建器
	 *
	 * @param string $table 表名
	 * @param string $data 数据
	 * @return Sk_Db_Query_Builder
	 */
	public function insert($table = NULL, $data = NULL)
	{
		return new Sk_Db_Query_Builder ( 'insert', $this, $table, $data );
	}

	/**
	 * update的sql构建器
	 *
	 * @param string $table 表名
	 * @param string $data 数据
	 * @return Sk_Db_Query_Builder
	 */
	public function update($table = NULL, $data = NULL)
	{
		return new Sk_Db_Query_Builder ( 'update', $this, $table, $data );
	}

	/**
	 * delete的sql构建器
	 *
	 * @param string $table 表名
	 * @return Sk_Db_Query_Builder
	 */
	public function delete($table = NULL)
	{
		return new Sk_Db_Query_Builder ( 'delete', $this, $table );
	}
}
