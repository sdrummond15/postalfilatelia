<?php
//

class Billiondigital_Theme_Model_Settings_Slider extends Varien_Object
{
    public function readOptions($themeName) {
        /** @var $helper Billiondigital_Core_Helper_Data */
        $helper = Mage::helper('billioncore');
        $this->setData('slider', unserialize($helper->getConfigValue("designer/settings/$themeName/slider", '')));
    }

    public function saveOptions($themeName, $postData) {

        $saveData = array();

        foreach ($postData as $row => $value) {
            // products|category|enabled|count|source|perslide|wdesktop|wlaptop|wtablet|wphone|header
            if (preg_match('#^slider_([^_]+)_([^_]+)$#',
                    $row, $match)) {
                if (!array_key_exists($match[2], $saveData)) {
                    $saveData[$match[2]] = array();
                }
                switch ($match[1]) {
                    case 'count':
                    case 'category':
                    case 'enabled':
                    case 'lg':
                    case 'md':
                    case 'sm':
                    case 'xs':
                        $value = strlen($value) ? intval($value) : '';
                        break;
                    case 'perslide':
                        $value = strlen($value) ? intval($value) : '4';
                        break;
                    case 'source':
                        $value = strlen($value) ? $value : Mage::helper('billiontheme/product')->CATEGORY;
                        break;
                }
                $saveData[$match[2]][$match[1]] = $value;
            }
        }

        Mage::getConfig()->saveConfig("designer/settings/$themeName/slider", serialize($saveData));
    }
}

//