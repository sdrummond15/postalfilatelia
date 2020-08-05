<?php
//

class Billiondigital_Theme_Block_Adminhtml_Settings_Edit_Tab_Logo extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fs = $form->addFieldset('form_logo', array('legend' => $this->__('Logo')));

        $fs->addField('logo_src', 'text', array(
            'label'     => $this->__('Logo Image Source'),
            'name'      => 'logo_src',
            'value'  => '',
            'tabindex' => 1
        ));

        $fs->addField('logo_url', 'text', array(
            'label' => $this->__('Logo Image Url'),
            'name' => 'logo_url',
            'value' => '',
            'tabindex' => 2
        ));

        $model = Mage::getSingleton('Billiondigital_Theme_Model_Settings_Logo');
        $model->readOptions(Mage::registry('designer_settings_theme'));
        $form->setValues($model->toArray());

        return parent::_prepareForm();
    }
}
//