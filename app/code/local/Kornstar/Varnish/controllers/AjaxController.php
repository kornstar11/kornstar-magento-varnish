<?php

class Kornstar_Varnish_AjaxController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
//		die('TIME:'.time());
		$getParms 		= $this->getRequest()->getParams();
		$productId		= $getParms['productid'];
		if($productId != '') {
			
			$product		= Mage::getModel('catalog/product')->load($productId);
			$return['product']	= $product->getData();
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($return));
		}
	}
}
