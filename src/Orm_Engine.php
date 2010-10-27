<?php
require_once("Orm_Settings.php");
/**
 * A basic orm class which uses PDO to provide simple and easy object relation mapping.
 * Note that every used object should have a variable $id and all vars should be public.
 *
 * @author j.smit <j.smit@sgoen.nl>
 */
class Orm_Engine
{
	/**
	 * @var array() $settings Holds options regarding the database connection.
	 */
	protected $settings;
	
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
		$this->transactionDate = null;
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
			throw new Exception("Class doesn't exist.");
		}

		$this->_connect();		

		$query     = ($where != null) ? "SELECT * FROM $table $where" : "SELECT * FROM $table";
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
			$query = "UPDATE $tableName SET "; 
			
			foreach($vars as $key => $value)
			{
				if($key != 'id')
				{
					$query = "$query $key=:$key, ";
				}
			}

			// remove the last comma and space.
			$query = substr($query, 0, -2);
			$query = "$query WHERE id = :id";
		}
		else
		{
			$query       = "INSERT INTO $tableName (";
			$queryValues = ") VALUES (";

			foreach($vars as $key => $value)
			{
				if($key != 'id')
				{
					$query       = "$query $key,";
					$queryValues = "$queryValues :$key,";
				}
			}
			
			// remove the last comma and space.
			$query       = substr($query, 0, -1);
			$queryValues = substr($queryValues, 0, -1);

			$query = "$query $queryValues)";

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
		$query     = "DELETE FROM $className WHERE id = :id";

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
		if($this->settings != null)
		{
			$dbType = $this->settings['db-type'];
			$dbHost = $this->settings['db-host'];
			$dbName = $this->settings['db-name'];
			$dbUser = $this->settings['db-user'];
			$dbPass = $this->settings['db-pass'];

			try
			{
				$this->pdo = new PDO("$dbType:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
			}
			catch(PDOException $e)
			{
				print_r($e);
			}
		}
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
