<?php

class Kornstar_Varnish_Model_Varnish_Observer {
	public function clearCache($obs) {
		$cacheTypes	=Mage::app()->getRequest()->getPost('types');
		if(isset($cacheTypes) && in_array('kornstarvarnish', $cacheTypes)) {
			try {
				$varnish	= Mage::getModel('kornstar_varnish/varnish_http_client')->flushAll();
				$res	= Mage::getModel('kornstar_varnish/resource_uri');
				$res	->truncateTable();
			}
			catch(Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			Mage::getSingleton('adminhtml/session')->addSuccess('Varnish is in the process of flushing it\'s cache.');
		}
	}
}
