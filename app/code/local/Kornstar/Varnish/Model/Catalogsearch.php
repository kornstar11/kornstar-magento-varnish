<?php class Kornstar_Varnish_Model_Catalogsearch extends Mage_Core_Model_Resource {
	public function getQueriesByProductId($productId) {
		$read	= $this->getConnection('core_read');
		$query	= "SELECT `q`.* 
				FROM 	`{$this->getTableName('catalogsearch/result')}` r
				INNER JOIN {$this->getTableName('catalogsearch/search_query')} q
				ON r.query_id = q.query_id
				WHERE	r.product_id = '{$productId}'
			  ";
		return $read->fetchAll($query);
	}
	
}
