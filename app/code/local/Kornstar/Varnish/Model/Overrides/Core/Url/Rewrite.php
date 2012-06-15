<?php
/**
This is for debugging only
*/
class Kornstar_Varnish_Model_Overrides_Core_Url_Rewrite extends Mage_Core_Model_Url_Rewrite {
	protected function _afterSave() {
		// Kornstar_Varnish_Model_Varnish_Http_Client
		$flushUrl	= Mage::getBaseUrl().$this->getData('request_path');
		//$client 	= Mage::getModel('kornstar_varnish/varnish_http_client');
		//die('test');
		Mage::log($flushUrl, null, 'benUrl.log');
		
		return parent::_afterSave();
		
		
		//die($flushUrl);
	}
}
