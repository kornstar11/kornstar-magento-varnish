<?php

class Kornstar_Varnish_Model_Varnish_Http_Client extends Zend_Http_Client {
	public function __construct($uri = 'http://127.0.0.1/', $config = null) {
		$this->config['useragent'] = 'Kornstar_Varnish_Model_Varnish_Http_Client';
		$this->setMethod(Zend_Http_Client::DELETE);
		// Check for trialing slash
		$uriLen	= strlen($uri);
		if(strpos($uri, '/') === FALSE)
			$uri .= '/';
		die($uri);
		return parent::__construct($uri, $config);
	}
	
	public function clearUrl($uri) { 
		$baseUri	= $this->getUri(true);
		$uri		= $baseUri.$uri;
		$this->setUri($uri);
		
		return $this->request();
	}
}
