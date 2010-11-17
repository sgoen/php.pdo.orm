<?php
require_once("Orm_AbstractSingletonDbDriver.php");
/**
 * Driver for a Mysql PDO object.
 * 
 * @author J.Smit <j.smit@sgoen.nl>
 */
class Orm_DbDriverMysql extends Orm_AbstractSingletonDbDriver
{
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
