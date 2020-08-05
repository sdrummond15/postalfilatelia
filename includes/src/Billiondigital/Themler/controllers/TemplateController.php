<?php
//

class Billiondigital_Themler_TemplateController extends Mage_Core_Controller_Front_Action
{
    public function defaultAction()
    {
        $this->loadLayout();
        $this->_title($this->__('Billion Themler'));
        $this->renderLayout();
    }
}

//