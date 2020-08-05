<?php
class Vsourz_Layerslider_Block_Adminhtml_Layerslider_Edit_Tabs_Content extends Mage_Adminhtml_Block_Widget_Form{
	 protected function _prepareForm() {
		if (Mage::registry('layerslider_data')) {
			$data = Mage::registry('layerslider_data')->getData();
		} else {
			$data = array();
		}
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('layerslider_layerslider', array('legend' => Mage::helper('layerslider')->__('Caption Information')));
		
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
		$wysiwygConfig->addData(array('add_variables' => false,
			'add_widgets' => false,
			'add_images' => false,
			'directives_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'),
			'directives_url_quoted' => preg_quote(Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive')),
			'widget_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'),
			'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
			'files_browser_window_width' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
			'files_browser_window_height' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height')
		));
		
		$fieldset->addField('slide_captionimg1', 'image', array(
          'label' => Mage::helper('layerslider')->__('Slide Caption Image1'),
		  'name' => 'slide_captionimg1',
		  'note' => '(*.jpg, *.jpeg, *.png, *.gif)',
        ));
		
		$fieldset->addField('slide_captionimg2', 'image', array(
          'label' => Mage::helper('layerslider')->__('Slide Caption Image2'),
		  'name' => 'slide_captionimg2',
		  'note' => '(*.jpg, *.jpeg, *.png, *.gif)',
        ));
		
		$fieldset->addField('slide_caption1', 'editor', array(
			'name' => 'slide_caption1',
			'label' => Mage::helper('layerslider')->__('Slide Caption1'),
			'title' => Mage::helper('layerslider')->__('Slide Caption1'),
			'style' => 'width:650px; height:100px;',
			'config' => $wysiwygConfig,
			'required' => false,
			'wysiwyg' => true
		));
		
		$fieldset->addField('slide_caption2', 'editor', array(
			'name' => 'slide_caption2',
			'label' => Mage::helper('layerslider')->__('Slide Caption2'),
			'title' => Mage::helper('layerslider')->__('Slide Caption2'),
			'style' => 'width:650px; height:100px;',
			'config' => $wysiwygConfig,
			'required' => false,
			'wysiwyg' => true
		));
		
		$fieldset->addField('slide_caption3', 'editor', array(
			'name' => 'slide_caption3',
			'label' => Mage::helper('layerslider')->__('Slide Caption3'),
			'title' => Mage::helper('layerslider')->__('Slide Caption3'),
			'style' => 'width:650px; height:100px;',
			'config' => $wysiwygConfig,
			'required' => false,
			'wysiwyg' => true
		));
		
		$fieldset->addField('slide_caption4', 'editor', array(
			'name' => 'slide_caption4',
			'label' => Mage::helper('layerslider')->__('Slide Caption4'),
			'title' => Mage::helper('layerslider')->__('Slide Caption4'),
			'style' => 'width:650px; height:100px;',
			'config' => $wysiwygConfig,
			'required' => false,
			'wysiwyg' => true
		));
		
		$fieldset->addField('slide_caption5', 'editor', array(
			'name' => 'slide_caption5',
			'label' => Mage::helper('layerslider')->__('Slide Caption5'),
			'title' => Mage::helper('layerslider')->__('Slide Caption5'),
			'style' => 'width:650px; height:100px;',
			'config' => $wysiwygConfig,
			'required' => false,
			'wysiwyg' => true
		));
		
		$form->setValues($data);
		return parent::_prepareForm();
	}
}