<?php
//

class Billiondigital_Themler_Model_Package_Package extends Mage_Connect_Package
{
    private $_themeName;

    public function __construct($source=null, $themeName = '')
    {
        $this->_themeName = $themeName;
        parent::__construct($source);
    }

    protected function _savePackage($path)
    {
        $fileName = $this->getReleaseFilename();
        if (is_null($this->_writer)) {
            $this->_writer = new Billiondigital_Themler_Model_Package_Writer($this->getContents(), $path.$fileName, $this->_themeName);
        }
        $this->_writer
            ->composePackage()
            ->addPackageXml($this->getDesignerPackageXml())
            ->archivePackage();
        return $this;
    }

    public function getDesignerPackageXml()
    {
        foreach ($this->_packageXml->contents->target as $target) {
            if ((string) $target['name'] === 'magedesign' || (string) $target['name'] === 'mageskin') {
                foreach ($target->xpath('dir[@name="frontend"]/dir[@name="default"]/dir') as $themeDir) {
                    $name = (string)$themeDir['name'];
                    if (preg_match('/_preview$/i', $name)) {
                        $themeDir['name'] = $this->_themeName . '_preview';
                    } else {
                        $themeDir['name'] = $this->_themeName;
                    }
                }
            }
        }

        return $this->_packageXml->asXML();
    }
}

//