<?php
//

class Billiondigital_Core_Controller_AdminAction extends Mage_Adminhtml_Controller_Action
{
    protected $coreHelper;
    protected $adminHelper;
    protected $currentTheme;

    protected function _construct() {
        $this->currentTheme = $this->getRequest()->getParam('theme');
        $this->coreHelper = Mage::helper('core');
        $this->adminHelper = Mage::helper('adminhtml');
    }

    protected function getAdminUrl($path, $params = array()) {
        return Mage::helper('core/url')->addRequestParam(
            $this->adminHelper->getUrl($path),
            $params
        );
    }

    protected function adminRedirect($path, $params = array()) {
        $this->getResponse()->setRedirect($this->getAdminUrl($path, $params));
    }

    protected function stripThemeName($theme)
    {
        return $theme ? substr(preg_replace('/[^a-z0-9_]/i', '', $theme), 0, 64) : $theme;
    }
}

//