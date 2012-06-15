<?php
// /Kornstar/Varnish/Model/Overrides/Catalog/Resource/Produc
class Kornstar_Varnish_Model_Overrides_Catalog_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection {
	protected function _initSelect() {
		//die('initSelect');
		return parent::_initSelect();
	}
	
	protected function _afterLoad() {
		$adminLocation	= (string)Mage::getConfig()->getNode("admin/routers/adminhtml/args")->frontName;
		//die($adminLocation);
		if(preg_match('/'.$adminLocation.'/i', $_SERVER['REQUEST_URI']) != 0) 
			return parent::_afterLoad();
		
		/**
			Populate the URI table	
		*/
//		die(print_r($_SERVER));
		$url	= 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$uri	= str_replace(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),'',$url);
		if($uri === '')
			return parent::_afterLoad();
//		$uri	= $_SERVER['REQUEST_URI'];
		foreach($this as $product) {
			/**
			$uriObj = Mage::getModel('kornstar_varnish/uri');
			$uriObj ->setProductId($product->getId());
			$uriObj ->setCategoryId('0');
			$uriObj ->setUri($uri);
			$uriObj ->save();
			*/
			$this->insertUri($product->getId(), '0', $uri);
			foreach($product->getCategoryIds() as $catId) {
				$this->insertUri($product->getId(), $catId, $uri);
				/**
				$uriObj	= Mage::getModel('kornstar_varnish/uri');
				$uriObj	->setProductId($product->getId());
				$uriObj	->setCategoryId($catId);
				$uriObj	->setUri($uri);
				$uriObj	->save();
				*/
			}
		}
		
		return parent::_afterLoad();
	}
	
	protected function insertUri($productId, $categoryId, $uri) {
		$uri		= urlencode($uri);
		$checkUri	= Mage::getModel('kornstar_varnish/uri')->getCollection()->addFilter('product_id',$productId)->addFilter('category_id',$categoryId)->addFilter('uri',$uri);
		
		if(count($checkUri) > 0 )
			return false;	

		$uriObj = Mage::getModel('kornstar_varnish/uri');
		$uriObj ->setProductId($productId);
		$uriObj ->setCategoryId($categoryId);
		$uriObj ->setUri($uri);
		$uriObj ->save();
		
		return true;

	}
}
