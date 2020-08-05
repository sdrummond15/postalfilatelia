<?php
//

class Billiondigital_Theme_Block_Messages extends Mage_Core_Block_Messages
{
    public function getGroupedHtml()
    {
        return Mage::helper('billiontheme/messages')->all($this->getMessages(), $this->_escapeMessageFlag);
    }
}

//