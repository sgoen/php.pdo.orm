<?php
/**
 * This file holds all the configurable variables for the orm.
 *
 * @author J.Smit <j.smit@sgoen.nl>
 */
class Orm_Config
{
	protected static $dbType = 'mysql';
	protected static $dbHost = 'localhost';
	protected static $dbName = 'test_orm';
	protected static $dbUser = 'root';
	protected static $dbPass = 'test';

	public static function getOptions()
	{
		$options = array(
			'db-type' => self::$dbType,
			'db-host' => self::$dbHost,
			'db-name' => self::$dbName,
			'db-user' => self::$dbUser,
			'db-pass' => self::$dbPass,
		);

		return $options;
	}
}
print_r(Orm_Config::getOptions());
?>
