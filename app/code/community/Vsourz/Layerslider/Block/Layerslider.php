<?php
class Vsourz_Layerslider_Block_Layerslider extends Mage_Catalog_Block_Product_Abstract{
	public function getSlides(){
		$_slideCollection = Mage::getModel("layerslider/layerslider")->getSlideCollection();
		return $_slideCollection;
	}
}