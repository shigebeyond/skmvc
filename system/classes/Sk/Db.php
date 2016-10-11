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
	 * 数据库连接配置
	 * @var array
	 */
	protected $_config;

	/**
	 * 当前事务的嵌套层级
	 * @var int
	 */
	protected $_transactionLevel = 0;

	/**
	 * pdo连接
	 * @var PDO
	 */
	protected $_pdo;

	public function __construct($config, $name = NULL)
	{
		$this->_config = $config['config'];

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
	 * 获得值的pdo类型
	 * @param unknown $value
	 * @return number
	 */
	protected function _value_type($value)
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
	 * Executes a prepared statement binding. This function uses integer indexes starting from zero
	 *<code>
	 * $statement = $db->prepare('SELECT * FROM robots WHERE name = :name');
	 * $result = $connection->executePrepared($statement, array('name' => 'mana'));
	 *</code>
	 *
	 * @param \PDOStatement $statement
	 * @param array         $bind
	 *
	 * @return \PDOStatement
	 * @throws \ManaPHP\Db\Exception
	 */
	protected function _executePrepared($statement, $bind)
	{
		foreach ($bind as $key => $value) {
			// 获得参数标识
			$parameter = $key; // 参数样式为 :name, 则直接使用
			if (is_int($key)) { // 参数样式为 ?, 则使用从1开始的数字
				$parameter = $key + 1;
			} 
			// 绑定参数
			$statement->bindValue($key, $value, $this->_value_type($value));
		}

		$statement->execute();

		return $statement;
	}

	/**
	 * Sends SQL statements to the database server returning the success state.
	 * Use this method only when the SQL statement sent to the server is returning rows
	 *<code>
	 *    //Querying data
	 *    $resultset = $connection->query("SELECT * FROM robots WHERE type='mechanical'");
	 *    $resultset = $connection->query("SELECT * FROM robots WHERE type=?", array("mechanical"));
	 *</code>
	 *
	 * @param string $sql
	 * @param array  $bind
	 * @param int    $fetchMode
	 *
	 * @return \PdoStatement
	 * @throws \ManaPHP\Db\Exception
	 */
	public function query($sql, $bind = [], $fetchMode = \PDO::FETCH_ASSOC)
	{
		try {
				$statement = $this->_pdo->prepare($sql);
				$statement = $this->_executePrepared($statement, $bind);

			$this->_affectedRows = $statement->rowCount();
			$statement->setFetchMode($fetchMode);
		} catch (\PDOException $e) {
			throw new DbException($e->getMessage());
		}

		return $statement;
	}

	/**
	 * Sends SQL statements to the database server returning the success state.
	 * Use this method only when the SQL statement sent to the server does n't return any rows
	 *<code>
	 *    //Inserting data
	 *    $success = $connection->execute("INSERT INTO robots VALUES (1, 'Boy')");
	 *    $success = $connection->execute("INSERT INTO robots VALUES (?, ?)", array(1, 'Boy'));
	 *</code>
	 *
	 * @param string $sql
	 * @param array  $bind
	 *
	 * @return int
	 * @throws \ManaPHP\Db\Exception
	 */
	public function execute($sql, $bind = [])
	{
		try {
				$statement = $this->_executePrepared($this->_pdo->prepare($sql), $bind);
				return $statement->rowCount();
		} catch (PDOException $e) {
			throw new Exception("执行sql出错: ", $e->getMessage());
		}
	}

	/**
	 * Returns the first row in a SQL query result
	 *<code>
	 *    //Getting first robot
	 *    $robot = $connection->fetchOne("SELECT * FROM robots");
	 *    print_r($robot);
	 *    //Getting first robot with associative indexes only
	 *    $robot = $connection->fetchOne("SELECT * FROM robots", \ManaPHP\Db::FETCH_ASSOC);
	 *    print_r($robot);
	 *</code>
	 *
	 * @param string $sql
	 * @param array  $bind
	 * @param int    $fetchMode
	 *
	 * @throws \ManaPHP\Db\Exception
	 * @return array|false
	 */
	public function fetchOne($sql, $bind = [], $fetchMode = \PDO::FETCH_ASSOC)
	{
		$result = $this->query($sql, $bind, $fetchMode);

		return $result->fetch();
	}

	/**
	 * Dumps the complete result of a query into an array
	 *<code>
	 *    //Getting all robots with associative indexes only
	 *    $robots = $connection->fetchAll("SELECT * FROM robots", \ManaPHP\Db::FETCH_ASSOC);
	 *    foreach ($robots as $robot) {
	 *        print_r($robot);
	 *    }
	 *  //Getting all robots that contains word "robot" withing the name
	 *  $robots = $connection->fetchAll("SELECT * FROM robots WHERE name LIKE :name",
	 *        ManaPHP\Db::FETCH_ASSOC,
	 *        array('name' => '%robot%')
	 *  );
	 *    foreach($robots as $robot){
	 *        print_r($robot);
	 *    }
	 *</code>
	 *
	 * @param string $sql
	 * @param array  $bind
	 * @param int    $fetchMode
	 *
	 * @throws \ManaPHP\Db\Exception
	 * @return array
	 */
	public function fetchAll($sql, $bind = [], $fetchMode = \PDO::FETCH_ASSOC)
	{
		$result = $this->query($sql, $bind, $fetchMode);

		return $result->fetchAll();
	}

	/**
	 * Inserts data into a table using custom SQL syntax
	 * <code>
	 * //Inserting a new robot
	 * $success = $connection->insert(
	 *     "robots",
	 *     array("Boy", 1952),
	 *     array("name", "year")
	 * );
	 * //Next SQL sentence is sent to the database system
	 * INSERT INTO `robots` (`name`, `year`) VALUES ("boy", 1952);
	 * </code>
	 *
	 * @param    string $table
	 * @param    array  $columnValues
	 *
	 * @return void
	 * @throws \ManaPHP\Db\Exception
	 */
	public function insert($table, $columnValues)
	{
		if (count($columnValues) === 0) {
			throw new DbException('Unable to insert into :table table without data'/**m07945f8783104be33*/, ['table' => $table]);
		}

		$escapedTable = $this->escapeIdentifier($table);
		if (array_key_exists(0, $columnValues)) {
			$insertedValues = rtrim(str_repeat('?,', count($columnValues)), ',');

			$sql = /** @lang Text */
			"INSERT INTO $escapedTable VALUES ($insertedValues)";
		} else {
			$columns = array_keys($columnValues);
			$insertedValues = ':' . implode(',:', $columns);
			$insertedColumns = '`' . implode('`,`', $columns) . '`';

			$sql = /** @lang Text */
			"INSERT INTO $escapedTable ($insertedColumns) VALUES ($insertedValues)";
		}

		$this->execute($sql, $columnValues);
	}

	/**
	 * Updates data on a table using custom SQL syntax
	 * <code>
	 * //Updating existing robot
	 * $success = $connection->update(
	 *     "robots",
	 *     array("name"),
	 *     array("New Boy"),
	 *     "id = 101"
	 * );
	 * //Next SQL sentence is sent to the database system
	 * UPDATE `robots` SET `name` = "boy" WHERE id = 101
	 * </code>
	 *
	 * @param    string       $table
	 * @param    array        $columnValues
	 * @param    string|array $conditions
	 * @param    array        $bind
	 *
	 * @return    int
	 * @throws \ManaPHP\Db\Exception
	 */
	public function update($table, $columnValues, $conditions, $bind = [])
	{
		$escapedTable = "`$table`";

		if (count($columnValues) === 0) {
			throw new DbException('Unable to update :table table without data'/**m07b005f0072d05d71*/, ['table' => $table]);
		}

		if (is_string($conditions)) {
			$conditions = [$conditions];
		}

		$wheres = [];

		/** @noinspection ForeachSourceInspection */
		foreach ($conditions as $k => $v) {
			if (is_int($k)) {
				$wheres[] = Text::contains($v, ' or ', true) ? "($v)" : $v;
			} else {
				$wheres[] = "`$k`=:$k";
				$bind[$k] = $v;
			}
		}

		$setColumns = [];
		foreach ($columnValues as $k => $v) {
			if (is_int($k)) {
				$setColumns[] = $v;
			} else {
				$setColumns[] = "`$k`=:$k";
				$bind[$k] = $v;
			}
		}

		$updateColumns = implode(',', $setColumns);
		$updateSql = /** @lang Text */
		"UPDATE $escapedTable SET $updateColumns WHERE " . implode(' AND ', $wheres);

		return $this->execute($updateSql, $bind);
	}

	/**
	 * Deletes data from a table using custom SQL syntax
	 * <code>
	 * //Deleting existing robot
	 * $success = $connection->delete(
	 *     "robots",
	 *     "id = 101"
	 * );
	 * //Next SQL sentence is generated
	 * DELETE FROM `robots` WHERE `id` = 101
	 * </code>
	 *
	 * @param  string       $table
	 * @param  string|array $conditions
	 * @param  array        $bind
	 *
	 * @return int
	 * @throws \ManaPHP\Db\Exception
	 */
	public function delete($table, $conditions, $bind = [])
	{
		if (is_string($conditions)) {
			$conditions = [$conditions];
		}

		$wheres = [];
		/** @noinspection ForeachSourceInspection */
		foreach ($conditions as $k => $v) {
			if (is_int($k)) {
				$wheres[] = Text::contains($v, ' or ', true) ? "($v)" : $v;
			} else {
				$wheres[] = "`$k`=:$k";
				$bind[$k] = $v;
			}
		}

		$sql = /**@lang Text */
		"DELETE FROM `$table` WHERE " . implode(' AND ', $wheres);

		return $this->execute($sql, $bind);
	}

	/**
	 * Appends a LIMIT clause to $sqlQuery argument
	 * <code>
	 *    echo $connection->limit("SELECT * FROM robots", 5);
	 * </code>
	 *
	 * @param    string $sql
	 * @param    int    $number
	 * @param   int     $offset
	 *
	 * @return    string
	 */
	public function limit($sql, $number, $offset = 0)
	{
		return $sql . ' LIMIT ' . $number . ($offset === 0 ? '' : (' OFFSET ' . $offset));
	}

	/**
	 * Active SQL statement in the object
	 *
	 * @return string
	 */
	public function getSQL()
	{
		return $this->_sql;
	}

	/**
	 * @param mixed $value
	 * @param int   $preservedStrLength
	 *
	 * @return int|string
	 */
	protected function _parseBindValue($value, $preservedStrLength)
	{
		if (is_string($value)) {
			if ($preservedStrLength > 0 && strlen($value) >= $preservedStrLength) {
				return $this->_pdo->quote(substr($value, 0, $preservedStrLength) . '...');
			} else {
				return $this->_pdo->quote($value);
			}
		} elseif (is_int($value)) {
			return $value;
		} elseif ($value === null) {
			return 'NULL';
		} elseif (is_bool($value)) {
			return (int)$value;
		} else {
			return $value;
		}
	}

	/**
	 * Active SQL statement in the object with replace the bind with value
	 *
	 * @param int $preservedStrLength
	 *
	 * @return string
	 */
	public function getEmulatedSQL($preservedStrLength = -1)
	{
		if (count($this->_bind) === 0) {
			return (string)$this->_sql;
		}

		$bind = $this->_bind;
		if (isset($bind[0])) {
			return (string)$this->_sql;
		} else {
			$replaces = [];
			foreach ($bind as $key => $value) {
				$replaces[':' . $key] = $this->_parseBindValue($value, $preservedStrLength);
			}

			return (string)strtr($this->_sql, $replaces);
		}
	}

	/**
	 * Active SQL statement in the object
	 *
	 * @return array
	 */
	public function getBind()
	{
		return $this->_bind;
	}

	/**
	 * 开始事务
	 * @return void
	 */
	public function begin()
	{
		if ($this->_transactionLevel === 0) {

			if (!$this->_pdo->beginTransaction()) {
				throw new DbException('beginTransaction failed.'/**m009fd54f98ae8b9d4*/);
			}
		}

		$this->_transactionLevel++;
	}

	/**
	 * Checks whether the connection is under a transaction
	 *<code>
	 *    $connection->begin();
	 *    var_dump($connection->isUnderTransaction()); //true
	 *</code>
	 *
	 * @return bool
	 */
	public function isUnderTransaction()
	{
		return $this->_pdo->inTransaction();
	}

	/**
	 * Rollbacks the active transaction in the connection
	 *
	 * @return void
	 * @throws \ManaPHP\Db\Exception
	 */
	public function rollback()
	{
		if ($this->_transactionLevel === 0) {
			throw new DbException('There is no active transaction'/**m05b2e1d48d574c125*/);
		}

		$this->_transactionLevel--;

		if ($this->_transactionLevel === 0) {

			if (!$this->_pdo->rollBack()) {
				throw new DbException('rollBack failed.'/**m0bf1d0a9da75bc040*/);
			}
		}
	}

	/**
	 * Commits the active transaction in the connection
	 *
	 * @return void
	 * @throws \ManaPHP\Db\Exception
	 */
	public function commit()
	{
		if ($this->_transactionLevel === 0) {
			throw new DbException('There is no active transaction'/**m0737d0edc3626fee3*/);
		}

		$this->_transactionLevel--;

		if ($this->_transactionLevel === 0) {

			if (!$this->_pdo->commit()) {
				throw new DbException('commit failed.'/**m0a74173017f21a198*/);
			}
		}
	}

	/**
	 * Returns insert id for the auto_increment column inserted in the last SQL statement
	 *
	 * @return int
	 */
	public function lastInsertId()
	{
		return (int)$this->_pdo->lastInsertId();
	}
}
