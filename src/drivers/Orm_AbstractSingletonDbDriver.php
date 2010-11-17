<?php
/**
 * Abstract class to be extended by driver classes, which then are Singleton objects.
 * 
 * @author J.Smit <j.smit@sgoen.nl>
 */
abstract class Orm_AbstractSingletonDbDriver
{
	/**
     * @var $instance Holds the instance of a driver class
	 */
	protected static $instance = null;

	/**
	 * Constructor is private to force Singleton usage.
	 */
	private function __construct(){}

	/**
	 * Uses late static binding to determine the called class of which
	 * an instance is created (when needed) and returned.
	 */
	public static function getInstance()
	{
		if(self::$instance == null)
		{
			$className      = get_called_class();			
			self::$instance = new $className();
		}

		return self::$instance;
	}

	abstract public function getPDO();
}
?>
