<?php
class Vsourz_Layerslider_Block_Adminhtml_Layerslider_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	public function __construct(){
		parent::__construct();
		$this->setId('layerslider_grid');
		$this->setDefaultSort('slide_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	protected function _prepareCollection(){
		$collection = Mage::getModel('layerslider/layerslider')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	} 
	protected function _prepareColumns(){
		 $this->addColumn('slide_id', array(
			'header' => Mage::helper('layerslider')->__('ID'),
			'align' => 'right',
			'width' => '10px',
			'index' => 'slide_id',
		));
		$this->addColumn('slide_title', array(
			'header' => Mage::helper('layerslider')->__('Title'),
			'align' => 'right',
			'width' => '100px',
			'index' => 'slide_title',
		));
		$this->addColumn('slide_url', array(
			'header' => Mage::helper('layerslider')->__('URL'),
			'align' => 'right',
			'width' => '100px',
			'index' => 'slide_url',
		));
		$this->addColumn('slide_img', array(
			'header' => Mage::helper('layerslider')->__('Image'),
			'align' => 'left',
			'width' => '250px',
			'index' => 'slide_img',
			'renderer' => 'layerslider/adminhtml_layerslider_renderer_image',
		));
		$this->addColumn('status', array(
			'header' => Mage::helper('layerslider')->__('Status'),
			'align' => 'left',
			'width' => '10px',
			'index' => 'status',
			'renderer' => 'layerslider/adminhtml_layerslider_renderer_status',
		));
		$this->addColumn('active_from', array(
			'header' => Mage::helper('layerslider')->__('active_from'),
			'align' => 'left',
			'type' => 'datetime',
			'width' => '10px',
			'index' => 'active_from',
		));
		$this->addColumn('active_to', array(
			'header' => Mage::helper('layerslider')->__('active_to'),
			'type' => 'datetime',
			'align' => 'left',
			'width' => '10px',
			'index' => 'active_to',
		));
		
	return parent::_prepareColumns();
	}
	protected function _prepareMassaction(){
		$this->setMassactionIdField('slide_id');
		$this->getMassactionBlock()->setFormFieldName('id');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('layerslider')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete', array('' => '')),        
			'confirm' => Mage::helper('layerslider')->__('Are you sure?')
		));
		return $this;
	}
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	
}