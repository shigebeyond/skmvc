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
class Sk_Db extends Singleton_Configurable 
{
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

	public function __construct($config, $name = NULL)
	{
		parent::__construct($config, $name);

		// 创建pdo连接
		$this->connect();
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
			throw new Exception("连接失败: " . $e->getMessage());
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
		unset(static::$_instances[$this->_name]);
	}
	
	
	/**
	 * 获得值的pdo类型
	 * @param unknown $value
	 * @return number
	 */
	public function pdo_type($value)
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
			$statement->bindValue($key, $value, $this->pdo_type($value));
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
	 * @return int
	 */
	public function execute($sql, $params = [])
	{
		try {
			// 执行sql + 返回影响行数
			return $this->_exec($sql, $params)->rowCount();
		} catch (PDOException $e) {
			throw new Exception("执行sql出错: ", $e->getMessage());
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
	 * @param int    $fetchMode
	 * @return Db_Result
	 */
	public function query($sql, $params = [], $fetchMode = PDO::FETCH_ASSOC)
	{
		try {
			// 执行sql
			$statement = $this->_exec($sql, $params);
			// 封装结果
			return new Db_Result($statement);
		} catch (PDOException $e) {
			throw new Exception("执行sql出错: ", $e->getMessage());
		}
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
				array('/(\?)/', '/:(\w+)/'),
				function($matches) use(&$i, $params){
					$i++;
					// 获得参数标识
					$key = $matches[1]; // 参数样式为 :name，则直接使用
					if($key == '?') // 参数样式为 ?, 则为数字
						$key = $i;
					
					// 若不存在该参数，则不转换
					if (!isset($params[$key]))
						return $matches[0];
					
					// 否则，转换
					return static::preview($params[$key]);
				},
				$sql
		);
	}
	
	/**
	 * 预览sql时，转换参数值
	 * 
	 * @param unknown $value
	 * @return unknown
	 */
	public static function preview_value($value)
	{
		if(is_string($value))
			return $this->quote($value);
		
		if(is_bool($value))
			return (int)$value;
		
		if($value === NULL)
			return 'NULL';
		
		return $value;
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
	 * 转义表名
	 * 
	 * @param string $table
	 * @return string
	 */
	public function quote_table($table)
	{
		return "`$table`";
	}
	
	/**
	 * 转义字段名
	 * 
	 * @param string $column
	 * @return string
	 */
	public function quote_column($column, $delimiter = ', ', $head = '(', $tail = ')')
	{
		if (is_array ( $column ))
		{
			// 转义
			$column = array_map(function($col){
				return "`$column`";
			}, $column);
			// 头部 + 分隔符拼接多值 + 尾部
			return $head . implode($delimiter, $column) . $tail;
		}
		
		return "`$column`";
	}
	
	/**
	* 转义值
	 *
	 * @param string|array $value
	 * @return string
	 */
	 public function quote($value, $param_type = NULL, $delimiter = ', ', $head = '(', $tail = ')')
	 {
		if (is_array ( $value )) 
		{
			// 转义
			$value = array_map(array($this->_pdo, 'quote'), $value );
			// 头部 + 分隔符拼接多值 + 尾部
			return $head . implode($delimiter, $value) . $tail;
		}
		
		// 转义
		return $this->_pdo->quote ( $value );
	}
}
