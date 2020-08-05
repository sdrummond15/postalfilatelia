<?php
class Vsourz_Layerslider_Model_Resource_Layerslider extends Mage_Core_Model_Mysql4_Abstract{
    public function _construct(){
        $this->_init('layerslider/layerslider','slide_id');
    }
}