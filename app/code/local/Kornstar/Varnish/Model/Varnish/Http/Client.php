<?php
/**
Ben Kornmeier 2012
*/
class Kornstar_Varnish_Model_Varnish_Http_Client extends Zend_Http_Client {
	protected $logFile		= 'null.log';
	protected $myName		= '';
	protected $clientRequestAddr 	= '127.0.0.1';
	protected $clientRequestPort 	= '80';
	protected $urlsToClear;
	protected $_urlHelper;
	

	public function __construct($params) {
		$uri			= (isset($params['uri']) ? $params['uri'] : 'http://127.0.0.1/');
		$config			= (isset($params['config']) ? $params['config'] : null);
		
		$this->logFile		= (string)Mage::getConfig()->getNode("kornstar_varnish_config")->log_file;
		$this->clientRequestAddr= (string)Mage::getConfig()->getNode("kornstar_varnish_config")->http_request_address;
		$this->clientRequestPort= (string)Mage::getConfig()->getNode("kornstar_varnish_config")->http_request_port;
		$this->myName		= get_class($this);
		
		$this->config['useragent'] = 'Kornstar_Varnish_Model_Varnish_Http_Client';
		$this->setMethod(Zend_Http_Client::DELETE);

		// Check for trialing slash
		$uriLen	= strlen($uri);
		if(strpos($uri, '/') === false)
			$uri .= '/';
		$this->_urlHelper       = Mage::helper('kornstar_varnish');
		$this->log('Started');
		
		return parent::__construct($uri, $config);
	}
	public function clearUrls($urls = false) {
		
		foreach($this->urlsToClear as $rewrite) {
			$resp   = $this->clearUrl($rewrite->getData('request_path'));
			$this->log($resp->getMessage());
		}
		
		return $this;
	}
	public function clearUrl($url) {
		try {
			$this->setUri($url);
			$return = $this->request();
		}
		catch(Exception $e) {
			die($e->__toString());
			$return = false;
		}
	
		if($return !== false)
			$this->log('CLEARED ---: '.$url);
		else
			$this->log('FAILED TO PURGE---: '.$url);
		return $return;
	}
	public function flushAll() {
		
		$this->setMethod(Zend_Http_Client::GET);
		$this->log('Flush all START---');
		return $this->clearUrl($this->_urlHelper->makeUrl('flushall'));
			
	}
	public function clearSearch($term) {
		$this->setMethod(Zend_Http_Client::GET);
		$this->log('Flush term ('.$term.') START---');
		$this->setHeaders('clearThis', $term);
		return $this->clearUrl('flushsearch');
	}
	public function clearAllSearch() {
		$this->setMethod(Zend_Http_Client::GET);
		$this->log('Flush all search START---');
		return $this->clearUrl('flushallsearch');
	}
	public function log($msg) {
		Mage::log($this->myName.': '.$msg, null, $this->logFile);
	}
}
