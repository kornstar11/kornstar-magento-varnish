<?php
class Kornstar_Varnish_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function makeUrl($uri) {
                $baseUri        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                $url            = $baseUri.$uri;

                return $url;
        }
	public function makeUri($url) {
                return str_replace(Mage::getBaseUrl(),'', $url);
        }


}

