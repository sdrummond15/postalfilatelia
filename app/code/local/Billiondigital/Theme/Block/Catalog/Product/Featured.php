<?php
//

class Billiondigital_Theme_Block_Catalog_Product_Featured extends Mage_Catalog_Block_Product_List
{
    protected function _beforeToHtml()
    {

    }

    protected function _toHtml()
    {
        Mage::helper('billiontheme')->setTemplateFallback($this);
        return parent::_toHtml();
    }
}

//