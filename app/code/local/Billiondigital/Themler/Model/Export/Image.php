<?php
//

class Billiondigital_Themler_Model_Export_Image extends Varien_Object
{
    private $imageStoragePath;

    function _construct()
    {
        $this->imageStoragePath = MAGENTO_ROOT . Mage::getSingleton('billioncore/config')->get('paths/export/images');
    }

    public function getFileName()
    {
        return preg_replace('/[^a-z0-9_\.]/i', '', $this->getId());
    }

    public function getContent()
    {
        $content = parent::getData('content');
        $storagePath = $this->imageStoragePath . '/' . $this->getFileName();
        if (!$content && file_exists($storagePath)) {
            $content = file_get_contents($storagePath);
        }
        return $content;
    }

    public function save()
    {
        $content = $this->getContent();
        if (!$content) return;
        $storagePath = $this->imageStoragePath . DS . $this->getFileName();

        Mage::helper('billioncore/filesystem')->write($storagePath, $content);
    }
}

//