<?php   
class Jaljale_Banner_Block_Index extends Mage_Core_Block_Template{   

	public function getBanner(){
		$collection = $this->getCollection();
		$collection->setOrder('id','DESC');
		$collection->addFieldToFilter('is_active','1');
		return $collection;
	}
	public function __construct()
    {
        parent::__construct();
        $collection = Mage::getModel('banner/slider')->getCollection();
        $this->setCollection($collection);
    }



}