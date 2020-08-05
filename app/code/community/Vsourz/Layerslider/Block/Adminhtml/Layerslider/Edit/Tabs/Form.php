<?php
class Vsourz_Layerslider_Block_Adminhtml_Layerslider_Edit_Tabs_Form extends Mage_Adminhtml_Block_Widget_Form{
	 protected function _prepareForm() {
		if (Mage::registry('layerslider_data')) {
			$data = Mage::registry('layerslider_data')->getData();
		} else {
			$data = array();
		}
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('layerslider_layerslider', array('legend' => Mage::helper('layerslider')->__('Slide Information')));
		$fieldset->addField('slide_title', 'text', array(
			'label' => Mage::helper('layerslider')->__('Slide Title'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'slide_title',
		));
		$fieldset->addField('slide_img', 'image', array(
          'label' => Mage::helper('layerslider')->__('Slide Image'),
		  'class' => 'required-entry required-file',
		  'required' => true,
		  'name' => 'slide_img',
		  'note' => '(*.jpg, *.jpeg, *.png, *.gif)',
        ));
		$fieldset->addField('slide_url', 'text', array(
			'label' => Mage::helper('layerslider')->__('Slide URL'),
			'required' => false,
			'name' => 'slide_url',
		));
		$fieldset->addField('active_from', 'date', array(
          'label' => Mage::helper('layerslider')->__('Active From'),
          'tabindex' => 1,
		  'name' => 'active_from',
          'image' => $this->getSkinUrl('images/grid-cal.gif'),
		  'time' => true,
          'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
		$fieldset->addField('active_to', 'date', array(
          'label' => Mage::helper('layerslider')->__('Active To'),
          'tabindex' => 1,
		  'name' => 'active_to',
          'image' => $this->getSkinUrl('images/grid-cal.gif'),
		  'time' => true,
          'format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
		$fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('layerslider')->__('Status'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'status',
          'value'  => '0',
          'values' => array('0' => 'Disable','1' => 'Enable'),
          'disabled' => false,
          'readonly' => false,
          'tabindex' => 1
        ));
		$form->setValues($data);
		return parent::_prepareForm();
	} 
}