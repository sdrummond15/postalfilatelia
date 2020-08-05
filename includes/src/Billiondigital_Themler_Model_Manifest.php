<?php

//

class Billiondigital_Themler_Model_Manifest
{
    private $_data;
    private $_version;

    protected $fso;

    public function __construct($args)
    {
        list($version, $data) = $args;
        $this->_version = $version;
        $this->_data = $data;

        $this->fso = Mage::helper('billioncore/filesystem');
    }

    public function getContent()
    {
        return $this->_data;
    }

    public function updateThemeVersion($theme)
    {
        if (strlen($this->_version)) {
            $this->fso->write(Mage::helper('billioncore/path')->themeVersion($theme), $this->_version);
        }
        return $this;
    }

    public function save()
    {
        $this->fso->write(self::getSharedPath($this->_version), $this->_data);
        return $this;
    }

    public static function getThemeVersion($theme)
    {
        $version = '';
        if (($path = Mage::helper('billioncore/path')->themeVersion($theme)) && file_exists($path))
        {
            $version = Mage::helper('billioncore/filesystem')->read($path);
        }
        return $version;
    }

    public static function open($version)
    {
        $data = '';
        if (self::exists($version)) {
            $data = Mage::helper('billioncore/filesystem')->read(self::getSharedPath($version));
        }
        return new self(array($version, $data));
    }

    public static function exists($version)
    {
        return file_exists(self::getSharedPath($version));
    }

    public static function restore($theme, $version)
    {
        $themeManifest = Mage::helper('billioncore/path')->themeManifest($theme, $version);
        if (!self::exists($version) && file_exists($themeManifest)) {
            Mage::helper('billioncore/filesystem')->copy($themeManifest, self::getSharedPath($version));
        }
    }

    public static function getUrl($version)
    {
        Mage::unregister('disableThemlerParams');
        Mage::register('disableThemlerParams', true);

        $url = Mage::helper('core/url')->addRequestParam(
            Mage::getUrl('billionthemler/adminhtml_theme/getManifest'),
            array('version' => $version)
        );

        Mage::unregister('disableThemlerParams');

        return $url;
    }

    public static function getSharedPath($version)
    {
        return Mage::helper('billioncore/path')->sharedManifest($version);
    }
}

//