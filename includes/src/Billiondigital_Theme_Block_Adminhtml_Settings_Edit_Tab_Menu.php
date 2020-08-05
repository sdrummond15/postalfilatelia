<?php
//

class Billiondigital_Theme_Block_Adminhtml_Settings_Edit_Tab_Menu extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fsh = $form->addFieldset('form_horizontal', array('legend' => $this->__('Horizontal')));

        $fsh->addField('hmenu', 'select', array(
            'label' => $this->__('View'),
            'name' => 'hmenu',
            'value' => '0',
            'values' => array(
                '0' => $this->__('All'),
                '1' => $this->__('Current')
            ),
            'tabindex' => 1
        ));

        $fsh->addField('navigation_home', 'select', array(
          'label' => $this->__('Show Home'),
          'name' => 'navigation_home',
          'value' => '0',
          'values' => array(
              '1' => $this->__('Yes'),
              '0' => $this->__('No')
          ),
          'tabindex' => 2
        ));

        $fsh->addField('navigation_home_text', 'text', array(
            'label' => Mage::helper('billiontheme')->__('Home Text'),
            'name' => 'navigation_home_text',
            'value' => 'Home',
            'tabindex' => 3
        ));

        $fsv = $form->addFieldset('form_vertical', array('legend' => $this->__('Vertical')));

        $fsv->addField('vmenu', 'select', array(
            'label' => $this->__('View'),
            'name' => 'vmenu',
            'value' => '0',
            'values' => array(
                '0' => $this->__('All'),
                '1' => $this->__('Current'),
                '2' => $this->__('None')
            ),
            'tabindex' => 4
        ));

        $model = Mage::getSingleton('Billiondigital_Theme_Model_Settings_Menu');
        $model->readOptions(Mage::registry('designer_settings_theme'));

        $form->setValues($model->toArray());

        return parent::_prepareForm();
    }
}
//