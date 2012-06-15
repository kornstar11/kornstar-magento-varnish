<?php
class Kornstar_Varnish_Model_Overrides_Cms_Page extends Mage_Cms_Model_Page {
	public function save() {
		if($this->getData('page_id') == false){
			$client         = Mage::getModel('kornstar_varnish/varnish_http_client', array());
			$client->flushAll();
		}
		else {
			$this->flushCache();
		}
			
                return parent::save();

	}
	public function delete() {
		$client         = Mage::getModel('kornstar_varnish/varnish_http_client', array());
		$client		->flushAll();
		return parent::delete();
	}
	protected function flushCache() {
		$oldData	= $this->getOrigData();
		$url		= Mage::getBaseUrl().$oldData['identifier'];
		$client         = Mage::getModel('kornstar_varnish/varnish_http_client', array());
		$client		->clearUrl($url);
		
                return parent::save();
	}
	


}
