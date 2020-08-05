<?php
//

class Billiondigital_Core_Helper_Path extends Mage_Core_Helper_Abstract
{
    protected static $APP_DIR;
    protected static $SKIN_DIR;
    protected $cfg;

    private $_cache = array();

    public function __construct()
    {
        $this->cfg = Mage::getSingleton('billioncore/config');
        self::$APP_DIR = str_replace('\\', '/', MAGENTO_ROOT) . $this->cfg->get('paths/app_base');
        self::$SKIN_DIR = str_replace('\\', '/', MAGENTO_ROOT) . $this->cfg->get('paths/skin_base');
    }

    public function appDir()
    {
        return self::$APP_DIR;
    }

    public function skinDir()
    {
        return self::$SKIN_DIR;
    }

    public function previewDirname($theme)
    {
        if (!($dir = $this->_getCache(__METHOD__, $theme))) {
            $dir = $this->cfg->get('paths/preview/dirname', array('theme' => $theme));
            $this->_setCache(__METHOD__, $theme, $dir);
        }

        return $dir;
    }

    public function themeDirname($theme)
    {
        if (!($dir = $this->_getCache(__METHOD__, $theme))) {
            $dir = $this->cfg->get('paths/theme/dirname', array('theme' => $theme));
            $this->_setCache(__METHOD__, $theme, $dir);
        }

        return $dir;
    }

    public function previewAppDir($theme)
    {
        return $this->appDir() . $this->previewDirname($theme);
    }

    public function previewSkinDir($theme)
    {
        return $this->skinDir() . $this->previewDirname($theme);
    }

    public function themeAppDir($theme)
    {
        return $this->appDir() . $this->themeDirname($theme);
    }

    public function themeModuleDir($theme)
    {
        return $this->appDir() . $this->themeDirname($theme) . '/module';
    }

    public function themePreviewModuleDir($theme) {
        return $this->appDir() . $this->previewDirname($theme) . '/module';
    }

    public function sharedManifest($version)
    {
        return MAGENTO_ROOT . $this->cfg->get('paths/export/manifest', array('version' => $version));
    }

    public function themeManifest($theme, $version)
    {
        return MAGENTO_ROOT . $this->cfg->get('paths/theme/manifest', array(
            'theme' => $theme,
            'version' => $version
        ));
    }


    public function themeVersion($theme)
    {
        return MAGENTO_ROOT . $this->cfg->get('paths/theme/version', array('theme' => $theme));
    }

    public function themeSkinDir($theme)
    {
        return $this->skinDir() . $this->themeDirname($theme);
    }

    private function _setCache($method, $key, $value)
    {
        if (!array_key_exists($method, $this->_cache)) {
            $this->_cache[$method] = array();
        }
        $this->_cache[$method][$key] = $value;
    }

    private function _getCache($method, $key, $default = null)
    {
        if (array_key_exists($method, $this->_cache) && array_key_exists($key, $this->_cache[$method])) {
            return $this->_cache[$method][$key];
        }
        return $default;
    }
}
//