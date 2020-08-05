<?php
//

class Billiondigital_Core_Model_Config extends Mage_Core_Model_Config_Base
{
    const CACHE_KEY_NAME = 'BILLIONDIGITAL_CORE';
    const CACHE_TAG_NAME = 'BILLIONDIGITAL_CORE';
    const CONFIGURATION_FILENAME = 'settings.xml';
    const CONFIGURATION_TEMPLATE = '<?xml version="1.0"?><config></config>';

    // TODO: CHECK CACHE IN ADMINHTML

    public function __construct($sourceData = null)
    {
        $tags = array(self::CACHE_TAG_NAME);
        $useCache = Mage::app()->useCache(self::CACHE_TAG_NAME);
        $this->setCacheId(self::CACHE_KEY_NAME);
        $this->setCacheTags($tags);

        if ($useCache && ($cache = Mage::app()->loadCache(self::CACHE_KEY_NAME))) {
            parent::__construct($cache);
        } else {
            parent::__construct(self::CONFIGURATION_TEMPLATE);
            Mage::getConfig()->loadModulesConfiguration(self::CONFIGURATION_FILENAME, $this);
            if ($useCache) {
                $xmlString = $this->getXmlString();
                Mage::app()->saveCache($xmlString, self::CACHE_KEY_NAME, $tags);
            }
        }
    }

    public function get($node, $params = null)
    {
        $nodeValue = $this->getNode($node);
        if (is_array($params)) {
            foreach ($params as $search => $replace) {
                $nodeValue = str_replace('{' . $search . '}', $replace, $nodeValue);
            }
        }
        return $nodeValue;
    }
}

//