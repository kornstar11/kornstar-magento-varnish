<?php
/**
Create our URI tracking table
*/ 
$installer = $this;
 
$installer->startSetup();
$installer->run("
CREATE TABLE `{$installer->getTable('kornstarvarnish/uri')}` (
`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`product_id` INT( 11 ) UNSIGNED NOT NULL ,
`category_id` INT( 11 ) UNSIGNED NOT NULL ,
`uri` VARCHAR(255) NOT NULL ,
KEY ( `id` ),
PRIMARY KEY ( `product_id` , `category_id`, `uri` ) 
) ENGINE = INNODB;
");

$installer->endSetup();
