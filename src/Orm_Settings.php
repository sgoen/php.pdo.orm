<?php
/**
 * This file holds all the settings for the orm.
 *
 * @author J.Smit <j.smit@sgoen.nl>
 */
class Orm_Settings
{
	public static $settings = array
	(
		// Connection settings
		'db-type' => 'mysql',
		'db-host' => 'localhost',
		'db-name' => 'test_orm',
		'db-user' => 'root',
		'db-pass' => 'test',
	
		// Query formats
		'query-select' => 'SELECT * FROM %TABLE% %WHERE%',
		'query-insert' => 'INSERT INTO %TABLE% (%FIELDS%) VALUES (%VALUES%)',
		'query-update' => 'UPDATE %TABLE% SET %UPDATES% WHERE %WHERE%',
		'query-delete' => 'DELETE FROM %TABLE% WHERE %WHERE%',
	);
}	
?>
