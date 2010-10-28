<?php
class Orm_DbDriverMysql implements Orm_iSingleton
{
	protected $dsn;
	protected $userName;
	protected $password;
	protected $driverOptions;	
	
	protected static $instance = null;

	// We could place all these functions in a parent when late-static binding is available.
	// now make it not static :P

	public static function getInstance()
	{
		if(self::$instance == null)
		{
			$className      = __CLASS__;			
			self::$instance = new $className();
		}

		return self::$instance;
	}

	public function __construct()
	{
	}

	public function getDsn()
	{
		return $this->dsn;
	}

	public function getUsername()
	{
		return $this->Username;
	}
	
	public function getPassword()
	{
		return $this->Password;
	}

	public function getDriverOptions()
	{
		return $this->driverOptions;
	}
}
?>
