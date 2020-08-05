<?php
//

class Billiondigital_Theme_Model_Settings_Menu extends Varien_Object
{
    public function readOptions($themeName) {
        /** @var $helper Billiondigital_Core_Helper_Data */
        $helper = Mage::helper('billioncore');
        $this->setData('hmenu', $helper->getConfigValue("designer/settings/$themeName/hmenu", ''));
        $this->setData('vmenu', $helper->getConfigValue("designer/settings/$themeName/vmenu", ''));
        $this->setData('navigation_home', $helper->getConfigValue("designer/settings/$themeName/navigation_home", ''));
        $this->setData('navigation_home_text', $helper->getConfigValue("designer/settings/$themeName/navigation_home_text", 'Home'));
    }

    public function saveOptions($themeName, $data) {
        if (isset($data['hmenu'])) {
            Mage::getConfig()->saveConfig("designer/settings/$themeName/hmenu", $data['hmenu']);
        }
        if (isset($data['vmenu'])) {
            Mage::getConfig()->saveConfig("designer/settings/$themeName/vmenu", $data['vmenu']);
        }
        if (isset($data['navigation_home'])) {
            Mage::getConfig()->saveConfig("designer/settings/$themeName/navigation_home", $data['navigation_home']);
        }
        if (isset($data['navigation_home_text'])) {
            Mage::getConfig()->saveConfig("designer/settings/$themeName/navigation_home_text", $data['navigation_home_text']);
        }
    }
}

//