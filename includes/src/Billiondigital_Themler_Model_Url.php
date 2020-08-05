<?php
//

require_once('Mage/Core/Model/Url.php');

class Billiondigital_Themler_Model_Url extends Mage_Core_Model_Url {

    public function getUrl($routePath = null, $routeParams = null) {
        if (!Mage::registry('disableThemlerParams')) {
            $theme = $this->getRequest()->getParam('theme');
            if (!$theme) {
                $theme = $this->getRequest()->getParam('template');
            }
            $preview = $this->getRequest()->getParam('preview');
            if ($theme) {
                $routeParams['_query']['theme'] = $theme;
            }
            if ($preview) {
                $routeParams['_query']['preview'] = $preview;
            }
        }
        return parent::getUrl($routePath, $routeParams);
    }

}

//