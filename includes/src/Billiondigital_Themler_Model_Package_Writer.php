<?php
//

class Billiondigital_Themler_Model_Package_Writer extends Mage_Connect_Package_Writer
{
    private $_themeName;

    public function __construct($files, $namePackage = '', $themeName = '')
    {
        $this->_themeName = $themeName;
        parent::__construct($files, $namePackage);
    }

    public function composePackage()
    {
        Mage::helper('billioncore/filesystem')->mkdir(self::PATH_TO_TEMPORARY_DIRECTORY, 0777, true);
        $root = self::PATH_TO_TEMPORARY_DIRECTORY . basename($this->_namePackage);
        Mage::helper('billioncore/filesystem')->mkdir($root, 0777, true);
        foreach ($this->_files as $file) {

            if (is_dir($file) || is_file($file)) {
                $fileName = basename($file);
                $filePath = dirname($file);
                if ($this->_themeName) {
                    $filePath = preg_replace(
                        '/^(\\.[\\/\\\]app[\\/\\\]design[\\/\\\]frontend[\\/\\\]default[\\/\\\])[^\\/\\\]+?(_preview|[\\/\\\]|$)/i',
                        '$1' . $this->_themeName . '$2',
                        $filePath
                    );
                    $filePath = preg_replace(
                        '/^(\\.[\\/\\\]skin[\\/\\\]frontend[\\/\\\]default[\\/\\\])[^\\/\\\]+?(_preview|[\\/\\\]|$)/i',
                        '$1' . $this->_themeName . '$2',
                        $filePath
                    );
                }
                Mage::helper('billioncore/filesystem')->mkdir($root . DS . $filePath, 0777, true);
                if (is_file($file)) {
                    copy($file, $root . DS . $filePath . DS . $fileName);
                } else {
                    Mage::helper('billioncore/filesystem')->mkdir($root . DS . $filePath . $fileName, 0777);
                }
            }
        }
        $this->_temporaryPackageDir = $root;
        return $this;
    }
}

//