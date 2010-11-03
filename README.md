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

Results are always returned in an array(). Even when getting something by a primary-key value.

	$orm   = new Orm_Core();
	$item = $orm->get('Item', "WHERE id = :id", array("id" => 1));

### Store an object

Storing an object is easy. It doesn't matter wether your object should be stored or updated. All storage works through the save() function.

	$orm  = new Orm_Core();
	$item = new Item();
	
	$item->foo = "bar";
	
	$orm->save($item);

### Remove an object

An object obtained from the orm can simply be removed by using the remove() function.

	$orm->remove($item);

### Using transactions