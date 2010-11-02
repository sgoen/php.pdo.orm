<?php
require_once("Orm_iSingletonDbDriver.php");
/**
 * Driver for a Mysql PDO object.
 * 
 * @author J.Smit <j.smit@sgoen.nl>
 */
class Orm_DbDriverMysql implements Orm_iSingletonDbDriver
{
	/**
	 * @var Orm_DbDriverMysql Singleton instance
	 */
	protected static $instance = null;

	/**
	 * Creates and returns an instance of the Orm_DbDriverMysql
	 *
	 * @return Orm_DbDriverMysql
	 */
	public static function getInstance()
	{
		if(self::$instance == null)
		{
			$className      = __CLASS__;			
			self::$instance = new $className();
		}

		return self::$instance;
	}

	/**
	 * Returns a Mysql PDO object.
	 * 
	 * @return PDO
	 */
	public function getPDO()
	{
		$settings = Orm_Settings::$settings;

		try
		{
			return new PDO("{$settings['db-type']}:host={$settings['db-host']};dbname={$settings['db-name']}", $settings['db-user'], $settings['db-pass']);
		}
		catch(PDOException $e)
		{
			throw new Exception("Orm_DbDriverMysql: Unable to connect to database.");
		}
	}
}
?>