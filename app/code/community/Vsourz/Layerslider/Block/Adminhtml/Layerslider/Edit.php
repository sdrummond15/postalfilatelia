<?php
class Vsourz_Layerslider_Block_Adminhtml_Layerslider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct(){
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'layerslider';
		$this->_controller = 'adminhtml_layerslider';
		$this->_mode = 'edit';
		$this->_updateButton('save', 'label', Mage::helper('layerslider')->__('Save Slide'));
		$this->_updateButton('delete', 'label', Mage::helper('layerslider')->__('Delete'));
		$this->_addButton('saveandcontinue', array(
			'label' => Mage::helper('layerslider')->__('Save And Continue Edit'),
			'onclick' => 'saveAndContinueEdit()',
			'class' => 'save',
		), -100); 
		
		$this->_formScripts[] ="
			function toggleEditor(){
				if (tinyMCE.getInstanceById('form_content') == null) {
					tinyMCE.execCommand('mceAddControl', false, 'edit_form');
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
				}
			}
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}"; 
	}
	protected function _prepareLayout(){
		parent::_prepareLayout();
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		}
	}
	public function getHeaderText() {
		if (Mage::registry('layerslider_data') && Mage::registry('layerslider_data')->getId()) {
			return Mage::helper('layerslider')->__('Edit Slide "%s"', $this->htmlEscape(Mage::registry('layerslider_data')->getSlideTitle()));
		} else {
			return Mage::helper('layerslider')->__('New Slide');
		}
	} 
}
