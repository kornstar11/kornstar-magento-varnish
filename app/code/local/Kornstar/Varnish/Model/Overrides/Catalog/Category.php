<?php
class Kornstar_Varnish_Model_Overrides_Catalog_Category extends Mage_Catalog_Model_Category {
	protected $registerKey  = 'kornstar_varnish_url_clear';
	public function save() {
		$container      = Mage::getModel('kornstar_varnish/urlcontainer');
		if($this->getData('entity_id') !== null) {
                        $container      ->coreUrlRewriteClearOn('category', $this->getData('entity_id'));
                        $container      ->kornstarVarnishUrlClearOn('category', $this->getData('entity_id'));
                }
                else
                        $container      ->setClearAll(true);
		Mage::register($this->registerKey, $container);
		return parent::save();
	}
	protected function _afterSave() {
                $container	= Mage::registry($this->registerKey);
		$container	->clearContents();

                return parent::_afterSave();
        }
	
	public function delete() {
                $container      = Mage::getModel('kornstar_varnish/urlcontainer');
                $container      ->coreUrlRewriteClearOn('category', $this->getData('entity_id'));
                $container      ->kornstarVarnishUrlClearOn('category', $this->getData('entity_id'));
                /* Delete the product*/
                $deleteReturn   = parent::delete();
                /* Now clean the cache */
                $container              ->clearContents();

                return $deleteReturn;

	}

}
	
