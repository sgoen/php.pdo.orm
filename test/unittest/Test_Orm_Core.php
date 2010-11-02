<?php
require_once("bootstrap.php");
/**
 * The orm should be unit tested with database access.
 *
 * Knows errors:
 *  - driver do not work since some db have specific query needs (sqlite)
 */
class Test_Orm_Core extends PHPUnit_Framework_TestCase
{
        protected $orm;

        public function setUp()
        {
                $sql = "DROP TABLE IF EXISTS `Item`;
                 CREATE TABLE `Item` (
                   `id` int(11) NOT NULL AUTO_INCREMENT,
                   `name` varchar(55) NOT NULL,
                   PRIMARY KEY (`id`)
                 ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0";

                $this->orm = new Orm_Core_T();
                $this->orm->query($sql);

                $itemOne = new Item();
                $itemTwo = new Item();

                $itemOne->name = "Foo";
                $itemTwo->name = "Bar";

                $this->orm->save($itemOne);
                $this->orm->save($itemTwo);
        }

        public function testGet()
        {
                $result = $this->orm->get("Item");
                $this->assertEquals(2, count($result));

                $result = $this->orm->get("Item", "WHERE name = :name", array("name" => "Foo"));
                $this->assertEquals(1, count($result));

                $result = $this->orm->get("Item", "WHERE name = :name", array("name" => "nonsense"));
                $this->assertEquals(0, count($result));
        }

		/**
		 * @expectedException Exception
		 */
        public function testGetException()
        {
                $this->orm->get("Nonsense");
        }

        public function testSave()
        {
                // save a new item with name
                $item = new Item();
                $item->name = "TestSave1";
                $this->orm->save($item);
                $this->assertEquals(1, count($this->orm->get('Item', "WHERE name = :name", array("name" => "TestSave1"))));
        }

		/**
		 * @expectedException Exception
		 */
        public function testSaveException()
        {
                // save a new item with name and id (not existing in db)
                $item = new Item();
                $item->id = 111;
                $item->name = "TestSaveException";
                $this->orm->save($item);
        }

        public function testRemove()
        {
                $result = $this->orm->get('Item');

                // remove an item and test the returned amount of items.
                $item = $result[0];
                $this->orm->remove($item);
                $result = $this->orm->get('Item');

                $this->assertEquals(1, count($result));

                // remove another item and test the returned amount of items.
                $item = $result[0];
                $this->orm->remove($item);
                $result = $this->orm->get('Item');

                $this->assertEquals(0, count($result));
        }
}
?>