pdp.pdo.orm
===========

## How to use

### Settings

### Get all objects

This example shows how to get all the objects from a table. Make sure there is a model with the same layout as the table.

	$orm   = new Orm_Core();
	$items = $orm->get('Item');
	
	foreach($items as $item)
	{
		// do something
	}

### Get a specific object

### Store an object

### Remove an object

### Using transactions