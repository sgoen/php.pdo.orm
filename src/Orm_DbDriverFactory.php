<?php
class Orm_DbDriverFactory
{
	public static function getDriver($driver)
	{
		switch($driver)
		{
			case 'mysql':
				return Orm_DbDriverMysql::getInstance();
			default:
				throw new Exception("Orm: Driver not found.");
		}
	}
}
?>
