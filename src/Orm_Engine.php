<?php
require_once("Orm_Settings.php");
require_once("Orm_DbDriverFactory.php");
/**
 * A basic orm class which uses PDO to provide simple and easy object relation mapping.
 * Note that every used object should have a variable $id and all vars should be public.
 *
 * @author j.smit <j.smit@sgoen.nl>
 */
class Orm_Engine
{
	/**
	 * @var PDO $pdo Holds the PDO object used for database interaction.
	 */
	protected $pdo;
	
	/**
	 * @var boolean $inTransaction Holds the current transaction state.
	 */
	protected $inTransaction;
	
	/**
	 * @var array() $transactionData Temporary holds all queries stored for a transaction.
	 */
	protected $transactionData;
	
	public function __construct()
	{
		$this->settings = Orm_Settings::$settings;
		$this->pdo = null;
		$this->inTransaction = false;
		$this->transactionData = null;
	}
	
	/**
	 * Gets the data from the given table.
	 *
	 * @param string $table The table from which data should be given
	 * @param string $where Customize output by entering extra serialized SQL statements
	 * @param array() $vars Contains the vars that should be replaced with the placeholders in the $where query
	 * @return array() $result
	 */
	public function get($table, $where = null, $vars = array())
	{
		if(!class_exists($table))
		{
			throw new Exception("Orm_Engine: Class doesn't exist.");
		}

		$this->_connect();		

		$query     = Orm_Settings::$settings['query-select'];
		$query     = preg_replace("/%TABLE%/", $table, $query);
		$query     = ($where != null) ? preg_replace("/%WHERE%/", $where, $query) : preg_replace("/%WHERE%/", "", $query);
		$statement = $this->pdo->prepare($query);
		
		$statement->execute($vars);
		
		$result = $statement->fetchAll(PDO::FETCH_CLASS, $table);

		$this->_disconnect();

		return $result;
	}
	
	/**
	 * Saves or updates a given object based on it's id.
	 *
	 * @note Needs rewriting!
	 */
	public function save($object)
	{
		$tableName = get_class($object);
		$vars      = $this->_getVariables($object);
		$query     = "";
		
		if(key_exists('id', $vars) && is_numeric($vars['id']))
		{
			$updates = ""; 
			foreach($vars as $key => $value)
			{
				if($key != 'id')
				{
					$updates = "$updates $key=:$key,";
				}
			}

			// remove the last comma
			$updates = substr($updates, 0, -1);
			
			$query = Orm_Settings::$settings['query-update'];
			$query = preg_replace("/%TABLE%/", $tableName, $query);
			$query = preg_replace("/%UPDATES%/", $updates, $query);
			$query = preg_replace("/%WHERE%/", "id = :id", $query);
		}
		else
		{
			$fields = "";
			$values = "";

			foreach($vars as $key => $value)
			{
				if($key != 'id')
				{
					$fields = "$fields $key,";
					$values = "$values :$key,";
				}
			}
			
			// remove the last comma
			$fields = substr($fields, 0, -1);
			$values = substr($values, 0, -1);
			
			$query = Orm_Settings::$settings['query-insert'];
			$query = preg_replace("/%TABLE%/", $tableName, $query);
			$query = preg_replace("/%FIELDS%/", $fields, $query);
			$query = preg_replace("/%VALUES%/", $values, $query);

			// unset id
			unset($vars['id']);
		}
		
		$this->_processStatement($query, $vars);
	}
	
	/**
	 * Removes the given object
	 *
	 * @param Object $object The object to remove from the database
	 */
	public function remove($object)
	{
		$tableName = get_class($object);
		$vars      = $this->_getVariables($object);
		$query     = Orm_Settings::$settings['query-delete'];
		$query     = preg_replace("/%TABLE%/", $tableName, $query);
		$query     = preg_replace("/%WHERE%/", "id = :id", $query);

		$this->_processStatement($query, array('id' => $vars['id']));
	}


	/**
	 * Start a transaction in which multiple queries can be executed.
	 */
	public function startTransaction()
	{
		$this->inTransaction = true;
	}

	/**
	 * Commit the started transaction, processes all the transactiondata.
	 */
	public function commitTransaction()
	{
		$this->_connect();
		$this->pdo->beginTransaction();

		foreach($this->transactionData as $data)
		{
			// quickfix
			foreach($data as $key => $value)
			{
				$statement = $this->pdo->prepare($key);
				$statement->execute($value);
			}
		}

		$this->pdo->commit();
		$this->_disconnect();

		$this->inTransaction = false;
		$this->transactionData = null;
	}

	/**
	 * Cancel a transaction, removing all the temporary data.
	 */
	public function cancelTransaction()
	{
		$this->inTransaction = false;
		$this->transactionData = null;	
	}
	
	protected function _connect()
	{
		$this->pdo = Orm_DbDriverFactory::getDriver(Orm_Settings::$settings['db-type'])->getPDO();
	}

	protected function _disconnect()
	{
		$this->pdo = null;
	}
	
	/**
	 * Processes a query wether it should be executed immediately or stored in a transaction.
	 *
	 * @param string $query The database query to be processed
	 */
	protected function _processStatement($query, $vars)
	{
		if($this->inTransaction)
		{
			$this->transactionData[] = array($query => $vars);
		}
		else
		{
			$this->_connect();

			$statement = $this->pdo->prepare($query);
			$statement->execute($vars);
			
			$this->_disconnect();
		}
	}

	/**
	 * Returns all the objects variables as an assosiative array.
	 *
	 * @param $object
	 * @return $result[] Array containing the variables and their values as key-value pairs.
	 */
	protected function _getVariables($class)
	{
		$reflect    = new ReflectionClass($class);
		$properties = $reflect->getProperties();
		$result     = array();

		foreach($properties as $property)
		{
			$property->setAccessible(true);
			$result[$property->getName()] = $property->getValue($class);
		}

		return $result;
	}
}
?>