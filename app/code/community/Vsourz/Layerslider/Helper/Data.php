<?php
class Vsourz_Layerslider_Helper_Data extends Mage_Core_Helper_Abstract{ 
	
	public function writeFile($file, $post, $field){
		$adminsession = Mage::getSingleton('adminhtml/session');
        $io = new Varien_Io_File();
		$io->open(array('path' => Mage::getBaseDir()));
		if ($io->fileExists($file))
        {
            if ($io->isWriteable($file))
            {
                try
                {
                    $io->streamOpen($file);
                    $io->streamWrite($post);

                } catch(Mage_Core_Exception $e)
                {
                    $adminsession->addError($e->getMessage());
                }
            } else {
            
                $adminsession->addError($file." is not writable. Change permissions to 644 to use this feature.");
            
            }
        } else {
            
            $adminsession->addError($file." does not exist. The file was not saved.");
        }
            
        $io->streamClose();
	}
	
	public function saveFileContentToConfig($file, $field){
		$adminsession = Mage::getSingleton('adminhtml/session');
		$io = new Varien_Io_File();
		$io->open(array('path' => Mage::getBaseDir()));
		if ($io->fileExists($file)){
            try{
				$contents = $io->read($file);
				Mage::getModel('core/config')->saveConfig('layerslider'.'/files/'.$field, $contents);
			}catch(Mage_Core_Exception $e){
				$adminsession->addError($e->getMessage());
			}
		}else{
			 $adminsession->addError($file." does not exist. Please create this file on your domain root to use this feature.");
		}
		$io->streamClose();
	}
	
	public function isWriteable($file)
    {
        $io = new Varien_Io_File();
        $io->open(array('path' => Mage::getBaseDir()));
        return $io->isWriteable($file);
    }
	
	public function cssFile(){
		$pckPath = Mage::getStoreConfig('layerslider/files/packagename'); 
		$skinUrl = Mage::getBaseDir()."/skin/";
		$absPath = "css/layerslider/layerstyle.css";
		$finalPath = $skinUrl.'frontend/'.$pckPath.'/'.$absPath;
		return $finalPath;
	}
}