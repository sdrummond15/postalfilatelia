<?php
class Vsourz_Layerslider_Block_Adminhtml_Layerslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	 public function __construct() {
	parent::__construct();
		$this->setId('layerslider_tabs');
		$this->setDestElementId('edit_form'); // this should be same as the form id define above
		$this->setTitle(Mage::helper('layerslider')->__('Slide Data'));
	}
	protected function _beforeToHtml() {
		$this->addTab('form_section', array(
			'label' => Mage::helper('layerslider')->__('Slide Basic Information'),
			'title' => Mage::helper('layerslider')->__('Slide Basic Information'),
			'content' => $this->getLayout()->createBlock('layerslider/adminhtml_layerslider_edit_tabs_form')->toHtml(),
		));
		$this->addTab('content_section', array(
			'label' => Mage::helper('layerslider')->__('Slide Caption Information'),
			'title' => Mage::helper('layerslider')->__('Slide Caption Information'),
			'content' => $this->getLayout()->createBlock('layerslider/adminhtml_layerslider_edit_tabs_content')->toHtml(),
		));
		return parent::_beforeToHtml();
	} 
}