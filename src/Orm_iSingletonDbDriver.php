<?php
interface Orm_iSingletonDbDriver
{
	public static function getInstance();
	public function getPDO();
}
?>
