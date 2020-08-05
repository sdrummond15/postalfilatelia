<?php
class Jaljale_Banner_Block_Adminhtml_Slider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("banner_form", array("legend"=>Mage::helper("banner")->__("Item information")));

								
						$fieldset->addField('image_url', 'image', array(
						'label' => Mage::helper('banner')->__('Image Link'),
						'name' => 'image_url',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						$fieldset->addField("image_link", "text", array(
						"label" => Mage::helper("banner")->__("Hyperlink"),
						"name" => "image_link",
						));
					
						$fieldset->addField("image_title", "text", array(
						"label" => Mage::helper("banner")->__("Title"),
						"name" => "image_title",
						));
					
						$fieldset->addField("image_caption", "textarea", array(
						"label" => Mage::helper("banner")->__("Caption"),
						"name" => "image_caption",
						));
									
						 $fieldset->addField('is_active', 'select', array(
						'label'     => Mage::helper('banner')->__('Is Active'),
						'values'   => Jaljale_Banner_Block_Adminhtml_Slider_Grid::getValueArray6(),
						'name' => 'is_active',					
						"class" => "required-entry",
						"required" => true,
						));

				if (Mage::getSingleton("adminhtml/session")->getSliderData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getSliderData());
					Mage::getSingleton("adminhtml/session")->setSliderData(null);
				} 
				elseif(Mage::registry("slider_data")) {
				    $form->setValues(Mage::registry("slider_data")->getData());
				}
				return parent::_prepareForm();
		}
}
