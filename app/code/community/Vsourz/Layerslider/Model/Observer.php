<?php

class Vsourz_Layerslider_Model_Observer extends Mage_Core_Model_Abstract {
       public function writeToFileOnConfigSave($observer) {
			$helper = Mage::helper('layerslider'); // loads the helper file
			$post = Mage::app()->getRequest()->getPost(); // gets all the data of that section
			$css_post = $post['groups']['files']['fields']['css']['value']; //gets the value of css area
			$helper->writeFile($helper->cssFile(), $css_post, 'css');

	   }
	   public function saveConfigOnConfigLoad(){
			$helper = Mage::helper('layerslider'); // loads the helper file
			$helper->saveFileContentToConfig($helper->cssFile(), 'css');
	   }
}