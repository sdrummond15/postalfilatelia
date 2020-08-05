<?php 
class Vsourz_Layerslider_Adminhtml_LayersliderController extends Mage_Adminhtml_Controller_action {
	protected function _initAction(){
		$this->loadLayout()->_setActiveMenu('layerslider/layerslider')->_addBreadcrumb(
			Mage::helper('adminhtml')->__('Layer Slider'),
			Mage::helper('adminhtml')->__('Slide Manager')
		);
		return $this;
	}
	public function indexAction(){
		$this->_initAction()->renderLayout();
	}
	public function newAction(){
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('layerslider/adminhtml_layerslider_edit'))->_addLeft($this->getLayout()->createBlock('layerslider/adminhtml_layerslider_edit_tabs'));
		$this->renderLayout(); 
	}
	public function saveAction(){
  	    if ($data = $this->getRequest()->getPost()){
			// Below _filterDateTime is needed to filter dates in Magento
			$data = $this->_filterDateTime($data, array('active_from', 'active_to'));
			$model = Mage::getModel('layerslider/layerslider');
			$id = $this->getRequest()->getParam('id');
			foreach ($data as $key => $value){
				if (is_array($value)){
					$data[$key] = implode(',',$this->getRequest()->getParam($key));
				}
			}
			if($id){
				$model->load($id);
			}
			//Code to Save Main Banner Image
			if(isset($_FILES['slide_img']['name']) && (file_exists($_FILES['slide_img']['tmp_name']))) {
				try{
					$uploader = new Varien_File_Uploader('slide_img');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
					$uploader->setAllowRenameFiles(false);
					// setAllowRenameFiles(true) -> move your file in a folder the magento way
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media').'/layerslider/';
					$imgName = explode('.',$_FILES['slide_img']['name']);
					$imgName[0] = $imgName[0].'-'.'slide-img'.'-'.date('Y-m-d H-i-s');
					$imgName = implode('.',$imgName);
					$imgName = preg_replace('/\s+/', '-', $imgName);
					$uploader->save($path, $imgName);
					$data['slide_img'] = 'layerslider/'.$imgName;
				}catch(Exception $e){
					
				}
			}
			else {       
				if(isset($data['slide_img']) && $data['slide_img']['delete'] == 1){
					// delete image file
					$image = explode(',',$data['slide_img']);
					unlink(Mage::getBaseDir('media').'/'.$image[1]);
					// set db blank entry
					$data['slide_img'] = ''; 
				}else{
					unset($data['slide_img']);
				}
			}
			
			//Caption Image 1
			if(isset($_FILES['slide_captionimg1']['name']) && (file_exists($_FILES['slide_captionimg1']['tmp_name']))) {
				try{
					$uploader = new Varien_File_Uploader('slide_captionimg1');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
					$uploader->setAllowRenameFiles(false);
					// setAllowRenameFiles(true) -> move your file in a folder the magento way
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media').'/layerslider/';
					$imgName = explode('.',$_FILES['slide_captionimg1']['name']);
					$imgName[0] = $imgName[0].'-'.'slide_captionimg1-img'.'-'.date('Y-m-d H-i-s');
					$imgName = implode('.',$imgName);
					$imgName = preg_replace('/\s+/', '-', $imgName);
					$uploader->save($path, $imgName);
					$data['slide_captionimg1'] = 'layerslider/'.$imgName;
				}catch(Exception $e){
					
				}
			}
			else {       
				if(isset($data['slide_captionimg1']) && $data['slide_captionimg1']['delete'] == 1){
					// delete image file
					$imagecap1 = explode(',',$data['slide_captionimg1']);
					unlink(Mage::getBaseDir('media').'/'.$imagecap1[1]);
					// set db blank entry
					$data['slide_captionimg1'] = ''; 
				}else{
				unset($data['slide_captionimg1']);
				}
			}
			
			//Caption Image 2
			if(isset($_FILES['slide_captionimg2']['name']) && (file_exists($_FILES['slide_captionimg2']['tmp_name']))) {
				try{
					$uploader = new Varien_File_Uploader('slide_captionimg2');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
					$uploader->setAllowRenameFiles(false);
					// setAllowRenameFiles(true) -> move your file in a folder the magento way
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media').'/layerslider/';
					$imgName = explode('.',$_FILES['slide_captionimg2']['name']);
					$imgName[0] = $imgName[0].'-'.'slide_captionimg2'.'-'.date('Y-m-d H-i-s');
					$imgName = implode('.',$imgName);
					$imgName = preg_replace('/\s+/', '-', $imgName);
					$uploader->save($path, $imgName);
					$data['slide_captionimg2'] = 'layerslider/'.$imgName;
				}catch(Exception $e){
					
				}
			}
			else {       
				if(isset($data['slide_captionimg2']) && $data['slide_captionimg2']['delete'] == 1){
					// delete image file
					$imagecap2 = explode(',',$data['slide_captionimg2']);
					unlink(Mage::getBaseDir('media').'/'.$imagecap2[1]);
					// set db blank entry
					$data['slide_captionimg2'] = ''; 
				}else{
				    unset($data['slide_captionimg2']);
				}
			}
			
			$model->setData($data);
			Mage::getSingleton('adminhtml/session')->setFormData($data);
			try{
				if ($id){
					$model->setId($id);
				}
				$model->save();
				if (!$model->getId()){
					Mage::throwException(Mage::helper('layerslider')->__('Error saving slide details'));
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('layerslider')->__('Details was successfully saved.'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
				}else{
					$this->_redirect('*/*/');
				}
			}catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				if ($model && $model->getId()) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
				} else {
					$this->_redirect('*/*/');
				} 
			}
			return;
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('layerslider')->__('No data found to save'));
		$this->_redirect('*/*/'); 
	}
	public function editAction(){
		$id = $this->getRequest()->getParam('id', null);
		$model = Mage::getModel('layerslider/layerslider');
		if($id){
			$model->load((int)$id);
			if($model->getId()){
				$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if($data){
				$model->setData($data)->setId($id);
			}
			}else{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('layerslider')->__('slide does not exist'));
				$this->_redirect('*/*/');
			}
		}
		Mage::register('layerslider_data', $model);
		$this->_title($this->__('Layerslider'))->_title($this->__('Edit Slide'));
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('layerslider/adminhtml_layerslider_edit'))
		->_addLeft($this->getLayout()->createBlock('layerslider/adminhtml_layerslider_edit_tabs'));
		$this->renderLayout(); 
	}
	public function deleteAction(){
		 if ($this->getRequest()->getParam('id') > 0) {
			try{
				$model = Mage::getModel('layerslider/layerslider');
				$id = $this->getRequest()->getParam('id');
				$objModel = $model->load($id);
				$path = Mage::getBaseDir('media');
				unlink($path.'/'.$objModel->SlideImg);
				unlink($path.'/'.$objModel->SlideCaptionimg1);
				unlink($path.'/'.$objModel->SlideCaptionimg2);
				$model->setId($id)->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			}catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/'); 
	}
	public function massDeleteAction(){
		// Here the id is got from the function _prepareMassAction in Grid.php. ($this->getMassactionBlock()->setFormFieldName('id');)
		$ids = $this->getRequest()->getParam('id');
		if(!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('layerslider')->__('Please select slide(s).'));
		}else{
			try{
				$slideModel = Mage::getModel('layerslider/layerslider');
				foreach($ids as $id){
					$objModel = $slideModel->load($id);
					$path = Mage::getBaseDir('media');
					unlink($path.'/'.$objModel->SlideImg);
					unlink($path.'/'.$objModel->SlideCaptionimg1);
					unlink($path.'/'.$objModel->SlideCaptionimg2);
					$objModel->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('layerslider')->__('Total of %d record(s) were deleted.', count($taxIds)));
			}catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
}