<?php
class Kornstar_Varnish_Model_Observer {
	protected	$cacheEnabled = true; 
	protected 	$obs;
	private		$cookieName ='beniscool';
	private 	$headerName ='Use-Cache';
	protected function isLoggedIn() {
		$customerSession = Mage::getSingleton('customer/session');
		if ($customerSession instanceof Mage_Customer_Model_Session  &&
		    $customerSession->isLoggedIn()) {
		    return true;
		}
		
		return false;
	}
	protected function cartEmpty() {
		if(Mage::helper('checkout/cart')->getCart()->getItemsCount() > 0)
			return false;
		return true;
	}
	protected function currencyIsUsd() {
		$store	= Mage::app()->getStore();
		if($store->getCurrentCurrencyCode() != $store->getDefaultCurrencyCode())
			return false;
		return true;
	}
	protected function canCache() {
		$types          = Mage::app()->getCacheInstance()->getTypes();
                $kornstarvarnish      = $types['kornstarvarnish'];
		if($this->cacheEnabled && $this->cartEmpty() && !$this->isLoggedIn() && $this->currencyIsUsd() && $kornstarvarnish->getStatus() === 1)
			return true;
		return false;
	}
	protected function setCacheCookie() {
		$this->getCookie()->set($this->cookieName, 'YES');
		return $this;
	}
	protected function deleteCacheCookie() {
		$this->getCookie()->delete($this->cookieName);
		return $this;
	}
	protected function setCacheHeader($val='Yes') {
		header($this->headerName.': '.$val);
		return $this;
	}

	public function process($obs) {
		$this->obs	= $obs;
		if($this->canCache()) {
			$this->setCacheHeader();
			$this->deleteCacheCookie();
		}
		else {
			$this->setCacheHeader('No');
			$this->setCacheCookie(); 
		}
		return $this;
	}
	public function getCookie() {
		return Mage::app()->getCookie();
	}

}
