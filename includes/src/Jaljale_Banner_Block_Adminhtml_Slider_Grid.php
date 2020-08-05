<?php

class Jaljale_Banner_Block_Adminhtml_Slider_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("sliderGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("banner/slider")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("banner")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));

				$this->addColumn("image", array(
				"header" => Mage::helper("banner")->__("Image"),
				"index" => "image_url",
				"renderer" => "jaljale_banner_Block_Adminhtml_Renderer_Image",
				));

				$this->addColumn("image_title", array(
				"header" => Mage::helper("banner")->__("Title"),
				"index" => "image_title",
				));
                
				$this->addColumn("image_link", array(
				"header" => Mage::helper("banner")->__("Hyperlink"),
				"index" => "image_link",
				));
				
				$this->addColumn('is_active', array(
				'header' => Mage::helper('banner')->__('Is Active'),
				'index' => 'is_active',
				'type' => 'options',
				'options'=>Jaljale_Banner_Block_Adminhtml_Slider_Grid::getOptionArray6(),				
				));
				

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);

			$this->getMassactionBlock()->addItem('remove_slider', array(
					 'label'=> Mage::helper('banner')->__('Remove Slider'),
					 'url'  => $this->getUrl('*/adminhtml_slider/massRemove'),
					 'confirm' => Mage::helper('banner')->__('Are you sure?')
				));

			$this->getMassactionBlock()->addItem('active', array(
					 'label'=> Mage::helper('banner')->__('Active Slider'),
					 'url'  => $this->getUrl('*/adminhtml_slider/massActive')
				));
			$this->getMassactionBlock()->addItem('inactive', array(
					 'label'=> Mage::helper('banner')->__('Inactive Slider'),
					 'url'  => $this->getUrl('*/adminhtml_slider/massInActive')
				));
			return $this;
		}
			
		static public function getOptionArray6()
		{
            $data_array=array(); 
			$data_array[0]='No';
			$data_array[1]='Yes';
            return($data_array);
		}
		static public function getValueArray6()
		{
            $data_array=array();
			foreach(Jaljale_Banner_Block_Adminhtml_Slider_Grid::getOptionArray6() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}