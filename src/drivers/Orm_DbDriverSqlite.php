<?php
require_once("Orm_AbstractSingletonDbDriver.php");
/**
 * Driver for a Sqlite PDO object.
 * 
 * @author J.Smit <j.smit@sgoen.nl>
 */
class Orm_DbDriverSqlite extends Orm_AbstractSingletonDbDriver
{
	/**
	 * Returns a Sqlite PDO object.
	 * 
	 * @return PDO
	 */
	public function getPDO()
	{
		$settings = Orm_Settings::$settings;

		try
		{
			 $db = new PDO("sqlite::memory");
		}
		catch(PDOException $e)
		{
			throw new Exception("Orm_DbDriverSqlite: Unable to connect to database.");
		}
	}
}
?>