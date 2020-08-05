<?php
class Vsourz_Layerslider_Model_Layerslider extends Mage_Core_Model_Abstract{
	public function _construct(){
		parent::_construct();
		$this->_init('layerslider/layerslider');
	}
	public function getSlideCollection(){
		$slideCollection = Mage::getModel('layerslider/layerslider')->getCollection()
		->addFieldToFilter('status','1')
		->addFieldToFilter('active_from',
                        array(
                         array('to' => Mage::getModel('core/date')->gmtDate()),
                                 array('active_from', 'null'=>'')))
		->addFieldToFilter('active_to', 
                        array(
                         array('from' => Mage::getModel('core/date')->gmtDate()),
                                 array('active_to', 'null'=>'')));
		//echo $slideCollection->getSelect()->__toString();
		//die;
		return $slideCollection;
	}
}