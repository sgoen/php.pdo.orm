<?php
/**
 * This files bootstraps all the things needed to run the unittests.
 * 
 * XXX TODO: check php version?
 */

require_once dirname(__FILE__).'/../../src/Orm_Core.php';

class Orm_Core_T extends Orm_Core
{
        public function query($sql)
        {
                $this->_connect();
                $this->pdo->query($sql);
                $this->_disconnect();
        }
}

class Item
{
        public $id;
        public $name;
}
?>