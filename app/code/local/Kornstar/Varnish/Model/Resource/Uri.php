<?php
class Kornstar_Varnish_Model_Resource_Uri extends Mage_Core_Model_Mysql4_Abstract{
    protected function _construct()
    {
        $this->_init('kornstarvarnish/uri', 'id');
    }   
    public function truncateTable() {
	$conn	= $this->_getWriteAdapter();
	$table	= $this->getTable('kornstarvarnish/uri');
	$conn	->query("TRUNCATE `{$table}`;");
	
	return $this;
    }
} 
