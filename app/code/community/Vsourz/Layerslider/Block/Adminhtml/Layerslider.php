<?php
class Vsourz_Layerslider_Block_Adminhtml_Layerslider extends Mage_Adminhtml_Block_Widget_Grid_Container{
	public function __construct(){
		$this->_controller = 'adminhtml_layerslider';
		$this->_blockGroup = 'layerslider';
		$this->_headerText = Mage::helper('layerslider')->__('Slide Manager');
		$this->_addButtonLabel = Mage::helper('layerslider')->__('Add Slide');
		parent::__construct(); 
	}
}