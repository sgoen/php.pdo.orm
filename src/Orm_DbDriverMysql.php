<?php
class Orm_DbDriverMysql implements Orm_iSingletonDbDriver
{
	protected static $instance = null;

	public static function getInstance()
	{
		if(self::$instance == null)
		{
			$className      = __CLASS__;			
			self::$instance = new $className();
		}

		return self::$instance;
	}

	public function getPDO()
	{
		$settings = Orm_Settings::$settings;

		try
		{
			return new PDO("{$settings['db-type']}:host={$settings['db-host']};dbname={$settings['db-name']}", $settings['db-user'], $settings['db-pass']);
		}
		catch(PDOException $e)
		{
			throw new Exception("Orm_Engine: Unable to connect to database.");
		}
	}
}
?>