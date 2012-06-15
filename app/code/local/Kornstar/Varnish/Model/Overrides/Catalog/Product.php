<?php
class Kornstar_Varnish_Model_Overrides_Catalog_Product extends Mage_Catalog_Model_Product {
	protected $registerKey	= 'kornstar_varnish_url_clear';

	public function save() {
		$productId	= $this->getData('entity_id');
		$container	= Mage::getModel('kornstar_varnish/urlcontainer');
		if($productId !== null) {
			$container	->coreUrlRewriteClearOn('product', $productId);
			$container	->kornstarVarnishUrlClearOn('product', $productId);
		}	
		else
			$container	->setClearAll(true);
		
		Mage::register($this->registerKey, $container);
		return parent::save();
	}
	
	protected function _afterSave() {
		/* Clear the actual product page */
		$container		= $this->getRegistry($this->registerKey);
		$container		->clearContents();
		return parent::_afterSave();
	}
	
	public function delete() {
		$productId	= $this->getData('entity_id');
		$container      = Mage::getModel('kornstar_varnish/urlcontainer');
		$container      ->coreUrlRewriteClearOn('product', $productId);
		$container      ->kornstarVarnishUrlClearOn('product', $productId);
		/* Delete the product*/
		$deleteReturn	= parent::delete();
		/* Now clean the cache */
		$container              ->clearContents();
	
		return $deleteReturn;
		
	}
	protected function getRegistry($key) {
		$val	= Mage::registry($key);
		Mage::unregister($key);
		return	$val;
	}
	protected function getSearchs() {
		$productId	= $this->getData('entity_id');
		$productSearchs	= Mage::getModel('kornstar_varnish/catalogsearch');
		$searchs	= $productSearchs->getQueriesByProductId($productId);
		return $searchs;
	}
	
}
