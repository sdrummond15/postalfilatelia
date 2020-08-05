<?php

class Nextend2_Nextend2_Model_Observer
{

    public function onPageCache($observer){
        if(Mage::app()->useCache('full_page')){
            $frontController = $observer->getEvent()->getFront();
            $this->buildCSSJS($frontController);
        }
    }

    public function buildCSSJS($observer) {
        static $once = false;
        if(!$once){
            $once = true;
            /** @var Mage_Core_Controller_Response_Http $response */
            $response = $observer->getResponse();
            Mage::dispatchEvent('n2_http_response_send_before', array('response' => $response));
    
            if (class_exists('N2AssetsManager', false)) {
                ob_start();
                if (class_exists('N2AssetsManager')) {
                    echo N2AssetsManager::getCSS();
                    echo N2AssetsManager::getJs();
                }
    
                $head = ob_get_clean();
                $response->setBody(preg_replace('/<\/head>/', $head . '</head>', $response->getBody(), 1));
            }
        }
    }

}