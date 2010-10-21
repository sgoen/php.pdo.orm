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
     * @param string $custom Customize output by entering extra SQL statements
     * @return array() $result
     */
	public function get($table, $custom = null)
	{
		if(class_exists($table))
		{
			$this->_connect();
			
			$query = "SELECT * FROM $table";

			if($custom != null)
			{
				$query = "$query $custom";
			}

			$pdoStatement = $this->pdo->query($query);
			$result = $pdoStatement->fetchAll(PDO::FETCH_CLASS, $table);

			$this->_disconnect();

			return $result;
		}
	}

	/**
	 * Saves or updates a given object based on it's id.
	 *
     * @note Needs rewriting!
     */
	public function save($object)
	{
		$className = get_class($object);
		$vars = get_class_vars($className);
		$query = "";
	
		if(isset($object->id) && $object->id > 0)
		{
			$changes = "";
			$iterator = 0;
			foreach($vars as $key => $value)
			{
				if($key != 'id' && $iterator < sizeof($vars) - 1)
				{
					$changes = $changes.$key.'="'.$object->$key.'", ';
				}
				elseif($iterator == sizeof($vars) - 1)
				{					
					$changes = $changes.$key.'="'.$object->$key.'"';
				}
				$iterator++;
			}
			$query = "UPDATE $className SET $changes WHERE id = $object->id";
		}
		else
		{
			$queryFields = "";
			$queryValues = "";

			$iterator = 0;
			foreach($vars as $key=>$value)
			{
				if($key != 'id' && $iterator < sizeof($vars) - 1)
				{
					$queryFields = "$queryFields$key, ";
					$objectValue = $object->$key;
					$queryValues = "$queryValues'$objectValue', ";
				}
				elseif($iterator == sizeof($vars) - 1)
				{
					$queryFields = "$queryFields$key";
					$objectValue = $object->$key;
					$queryValues = "$queryValues'$objectValue'";
				}
				$iterator++;
			}

			$query = "INSERT INTO $className($queryFields) VALUES ($queryValues)";
		}
		
		$this->_processQuery($query);
	}

	/**
     * Removes the given object
	 *
	 * @param Object $object The object to remove from the database
	 */
	public function remove($object)
	{
		$className = get_class($object);
		$query = "DELETE FROM $className WHERE id = $object->id";

		$this->_processQuery($query);
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
			$this->databaseHandler->exec($data);
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
				$this->databaseHandler = new PDO("$dbType:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
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
	protected function _processQuery($query)
	{
		if($this->inTransaction)
		{
			$this->transactionData[] = $query;
		}
		else
		{
			$this->_connect();
			$this->pdo->exec($query);
			$this->_disconnect();
		}
	}	
}
?>

