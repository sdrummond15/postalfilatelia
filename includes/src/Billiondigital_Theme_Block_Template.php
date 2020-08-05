<?php
//

class Billiondigital_Theme_Block_Template extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        Mage::helper('billiontheme')->setTemplateFallback($this);

        return parent::_toHtml();
    }
}

//