<?php
//

class Billiondigital_Themler_Model_Package_Extension extends Mage_Connect_Model_Extension
{
    private $_themeName;

    public function __construct($args = array())
    {
        $this->_themeName = array_key_exists('themeName', $args) ? $args['themeName'] : '';
    }

    protected function getPackage()
    {
        if (!$this->_package instanceof Billiondigital_Themler_Model_Package_Package) {
            $this->_package = new Billiondigital_Themler_Model_Package_Package(null, $this->_themeName);
        }
        return $this->_package;
    }
}

//