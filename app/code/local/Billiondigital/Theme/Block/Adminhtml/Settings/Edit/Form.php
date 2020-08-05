<?php
//

class Billiondigital_Theme_Block_Adminhtml_Settings_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => Mage::helper('core/url')->addRequestParam(
                    $this->getUrl('*/*/save'),
                    array('theme' => $this->getRequest()->getParam('theme'))
                ),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

//