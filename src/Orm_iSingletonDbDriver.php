<?php
/**
 * Simple interface for a SingletonDbDriver object
 * 
 * @author J.Smit <j.smit@sgoen.nl>
 */
interface Orm_iSingletonDbDriver
{
	public static function getInstance();
	public function getPDO();
}
?>
