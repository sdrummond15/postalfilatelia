<?php
//

class Billiondigital_Theme_Block_Adminhtml_Settings_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle($this->__('Theme Settings'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => $this->__('Categories'),
            'title'     => $this->__('Categories'),
            'content'   => $this->getLayout()->createBlock('billiontheme/adminhtml_settings_edit_tab_menu')->toHtml(),
        ));

        $this->addTab('logo_section', array(
            'label'     => $this->__('Logo'),
            'title'     => $this->__('Logo'),
            'content'   => $this->getLayout()->createBlock('billiontheme/adminhtml_settings_edit_tab_logo')->toHtml()
        ));

        $this->addTab('slider_section', array(
            'label'     => $this->__('Sliders'),
            'title'     => $this->__('Sliders'),
            'content'   => $this->getLayout()->createBlock('billiontheme/adminhtml_settings_edit_tab_slider')->toHtml()
        ));

        return parent::_beforeToHtml();
    }
}


//