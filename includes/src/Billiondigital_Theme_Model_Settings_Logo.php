<?php
//

class Billiondigital_Theme_Model_Settings_Logo extends Varien_Object
{
    public function readOptions($themeName) {
        /** @var $helper Billiondigital_Core_Helper_Data */
        $helper = Mage::helper('billioncore');
        $this->setData('logo_src', $helper->getConfigValue("designer/settings/$themeName/logo_src", ''));
        $this->setData('logo_url', $helper->getConfigValue("designer/settings/$themeName/logo_url", ''));
    }

    public function saveOptions($themeName, $data) {
        if (isset($data['logo_src'])) {
            Mage::getConfig()->saveConfig("designer/settings/$themeName/logo_src", $data['logo_src']);
        }
        if (isset($data['logo_url'])) {
            Mage::getConfig()->saveConfig("designer/settings/$themeName/logo_url", $data['logo_url']);
        }
    }
}

//