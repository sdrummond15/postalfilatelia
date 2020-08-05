<?php
class Vsourz_Layerslider_Block_Adminhtml_Layerslider_Renderer_Image extends
Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row){
		$value = $row->getData($this->getColumn()->getIndex());
		return '<img width="200" height="100" src="'.Mage::getBaseUrl('media').$value . '" />';
	}
}