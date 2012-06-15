<?php
class Kornstar_Varnish_Model_Urlcontainer {
	protected $_coreUrlRewrites;
	protected $_kornstarVarnishUrls;
	protected $_client;
	protected $_urlHelper;
	protected $_clearAll;
	public function __construct() {
		$this->_urlHelper	= Mage::helper('kornstar_varnish');
		$this->_client		= Mage::getModel('kornstar_varnish/varnish_http_client', array());
		return $this;
	}
	public function coreUrlRewriteClearOn($type, $id) {
                if($type == 'category') {
                        $rewrites       = Mage::getModel('core/url_rewrite')->getCollection()->addFilter('category_id', $id);
                }
                else {
                        $rewrites       = Mage::getModel('core/url_rewrite')->getCollection()->addFilter('product_id', $id);
                }
		$this->_coreUrlRewrites	= $rewrites;
	
                return $this;
        }
	
	public function kornstarVarnishUrlClearOn($type, $id) {
		if($type === 'product')
			$uriCollection  = Mage::getModel('kornstar_varnish/uri')->getCollection()->addFilter('product_id', $id)->addFilter('category_id', '0');
		else
			$uriCollection  = Mage::getModel('kornstar_varnish/uri')->getCollection()->addFilter('category_id', $id);
		$this->_kornstarVarnishUrls	= $uriCollection;

		return $this;
	}
	public function clearContents() {
		if($this->_clearAll === true) {
			$this->_client		->flushAll();
			return $this;
		}
		
		if(count($this->_kornstarVarnishUrls)) {
			foreach($this->_kornstarVarnishUrls as $kornstarUrl) {
				$uri	= $kornstarUrl->getUri();
				$return	= $this->_client->clearUrl($this->_urlHelper->makeUrl(urldecode($uri)));
				if($return !== false) {
					echo 'CP:CLEARED: '.$this->_urlHelper->makeUrl(urldecode($uri)).chr(10);
					$kornstarUrl->delete();
				}
			}
		}
		
		if(count($this->_coreUrlRewrites)) {
			foreach($this->_coreUrlRewrites as $coreRewrite) {
				$requestPath	= $coreRewrite->getRequestPath();
				$return = $this->_client->clearUrl($this->_urlHelper->makeUrl(urldecode($requestPath)));
				if($return !== false) {
					echo 'CLEARED: '.$this->_urlHelper->makeUrl(urldecode($requestPath)).chr(10);
				}
			}
		}
		//$this->_client	->clearAllSearch();
		return $this;
	}
	public function setClearAll($value) {
		$this->_clearAll	= $value;
		return $this;	
	}
	public function getClient() {
		return $this->_client;
	}

}
