<?php
//

class Billiondigital_Theme_Model_Theme extends Varien_Object
{
    public function getSettingsUrl() {
        return $this->getUrl('billiontheme/adminhtml_settings/index');
    }

    public function getPublishUrl() {
        return $this->getUrl('billionthemler/adminhtml_theme/publish');
    }

    public function getPreviewUrl() {
        $previewLink = Mage::helper('core/url')->addRequestParam(
            Mage::getBaseUrl(),
            array('theme' => $this->getName())
        );
        return $previewLink;
    }

    public function getEditUrl() {
        $params = array('theme' => $this->getName());

        if (strlen($version = Billiondigital_Themler_Model_Manifest::getThemeVersion($this->getName()))) {
            $params['ver'] = $version;
        }

        Mage::unregister('disableThemlerParams');
        Mage::register('disableThemlerParams', true);

        $url = Mage::helper('core/url')->addRequestParam(
            Mage::getUrl('billionthemler/adminhtml_theme/index'),
            $params
        );

        Mage::unregister('disableThemlerParams');

        return $url;
    }

    public function isActive() {
        return Mage::helper('billioncore')->isActive($this->getName());
    }

    public function getUrl($route, $params = array()) {
        $params['theme'] = $this->getName();
        return Mage::helper('core/url')->addRequestParam(
            Mage::helper('adminhtml')->getUrl($route),
            $params
        );
    }
}

//