<?php
class Jaljale_Banner_Model_Mysql4_Slider extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("banner/slider", "id");
    }
}