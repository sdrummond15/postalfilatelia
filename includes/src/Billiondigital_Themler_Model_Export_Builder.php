<?php
//

class Billiondigital_Themler_Model_Export_Builder
{
    private static $APP_RESULT;
    private static $SKIN_RESULT;

    private static $THEME_APP;
    private static $THEME_SKIN;

    private $_themeName;

    /**
     * @var Billiondigital_Core_Helper_Filesystem
     */
    private $_fso;
    /**
     * @var Billiondigital_Core_Model_Config
     */
    private $_cfg;
    /**
     * @var Billiondigital_Core_Helper_Path
     */
    private $_path;

    /**
     * @var array
     */
    private $_theme;
    private $_themeImages = array();
    private $_themeFonts = array();

    /**
     * @param array $args
     */
    public function __construct($args)
    {
        if (!is_array($args) || !isset($args['name']) || !isset($args['content']) || !isset($args['dirname']))
            throw new Mage_Core_Exception('Missing parameters');

        $this->_cfg = Mage::getSingleton('billioncore/config');
        $this->_fso = Mage::helper('billioncore/filesystem');
        $this->_path = Mage::helper('billioncore/path');

        $this->_themeName = $args['name'];
        $content = $args['content'];
        $dirname = $args['dirname'];

        self::$APP_RESULT = MAGENTO_ROOT
            . $this->_cfg->get('paths/export/result')
            . $this->_cfg->get('paths/app_base')
            . $dirname;
        self::$SKIN_RESULT = MAGENTO_ROOT
            . $this->_cfg->get('paths/export/result')
            . $this->_cfg->get('paths/skin_base')
            . $dirname;

        self::$THEME_APP = MAGENTO_ROOT
            . $this->_cfg->get('paths/app_base')
            . $dirname;
        self::$THEME_SKIN = MAGENTO_ROOT
            . $this->_cfg->get('paths/skin_base')
            . $dirname;

        if (array_key_exists('themeFso', $content)) {
            $this->_theme = $content['themeFso'];
        }
        if (array_key_exists('images', $content)) {
            $this->_themeImages = $content['images'];
        }
        if (array_key_exists('iconSetFiles', $content)) {
            $this->_themeFonts = $content['iconSetFiles'];
        }
    }

    public function build()
    {
        $this->_fso->remove(MAGENTO_ROOT . $this->_cfg->get('paths/export/temp'), true);

        $fsoPath = MAGENTO_ROOT . $this->_cfg->get('paths/export/fso');

        Mage::helper('billionthemler/export')->unpackFso($this->_theme, $fsoPath);

        if (is_dir($fsoPath . '/app'))
            $this->_fso->copy($fsoPath . '/app', self::$APP_RESULT);

        if (is_dir($fsoPath . '/skin'))
            $this->_fso->copy($fsoPath . '/skin', self::$SKIN_RESULT);

        if (is_dir($fsoPath . '/includes'))
            $this->_fso->copy($fsoPath . '/includes', self::$APP_RESULT . '/template/designer');

        $this->_fso->remove($fsoPath . '/app', true);
        $this->_fso->remove($fsoPath . '/skin', true);
        $this->_fso->remove($fsoPath . '/module', true);

        $this->_processImages();
        $this->_processFonts();
        $this->_processThemeFiles();
        $this->_postProcessTheme();

    }

    /**
     * Saves theme images
     * @return array Images css replace info
     */
    private function _processImages() {
        $images = Mage::getSingleton('billionthemler/export_imageList');

        foreach ($this->_themeImages as $id => $data) {
            $images->add($id, $data);
        }

        $images->export(self::$SKIN_RESULT . '/images/designer');
    }

    /**
     * Saves theme fonts
     */
    private function _processFonts() {
        $fonts = Mage::getSingleton('billionthemler/export_fontList');

        foreach ($this->_themeFonts as $id => $data) {
            $fonts->add($id, $data);
        }

        $fonts->export(self::$SKIN_RESULT);
    }

    /**
     * Processes designer specific files
     */
    private function _processThemeFiles() {
        $fsoPath = MAGENTO_ROOT . $this->_cfg->get('paths/export/fso');
        $replaceInfo = array();
        $replaceInfo = array_merge_recursive(
            $replaceInfo,
            (array) Mage::getSingleton('billionthemler/export_fontList')->getCssReplaceInfo()
        );
        $replaceInfo = array_merge_recursive(
            $replaceInfo,
            (array) Mage::getSingleton('billionthemler/export_imageList')->getCssReplaceInfo()
        );

        foreach($this->_fso->enumerate($fsoPath) as $file) {
            $dest = self::$SKIN_RESULT . str_replace($fsoPath, '', $file);
            $info = pathinfo($dest);

            $fileExt = isset($info['extension']) && $info['extension'] ? $info['extension'] : '';

            if (!in_array($fileExt, array('png', 'jpg', 'bmp', 'ico', 'gif', 'jpeg', 'css', 'js'))) continue;

            if (!is_dir($info['dirname']))
                mkdir($info['dirname'], 0777, true);

            if ($fileExt === 'css') {
                $content = file_get_contents($file);
                $content = str_replace($replaceInfo['from'], $replaceInfo['to'], $content);
                file_put_contents($dest, $content);
            } else {
                copy($file, $dest);
            }
        }
    }

    private function _postProcessTheme() {
        $tempResult = MAGENTO_ROOT . $this->_cfg->get('paths/export/result');
        $htmlReplaceInfo = Mage::getSingleton('billionthemler/export_imageList')->getHtmlReplaceInfo();
        $app = $this->_fso->enumerate(self::$APP_RESULT);
        $skin = $this->_fso->enumerate(self::$SKIN_RESULT);
        $themeFiles = array_merge($app, $skin);

        foreach ($themeFiles as $file) {
            $info = pathinfo($file);

            $fileExt = isset($info['extension']) && $info['extension'] ? $info['extension'] : '';
            $content = file_get_contents($file);
            if ($content === '[DELETED]') {
                $previewFile = str_replace($tempResult, MAGENTO_ROOT, $file);
                unlink($file);
                if (is_readable($previewFile)) {
                    unlink($previewFile);
                }
            } else {
                if (!in_array($fileExt, array('phtml'))) continue;
                $content = str_replace($htmlReplaceInfo['from'], $htmlReplaceInfo['to'], $content);
                $content = preg_replace('#src=["\']url\((https?://[^\)]+)\)["\']#', 'src="$1"', $content);

                file_put_contents($file, $content);
            }
        }

        $this->_updateDiff($themeFiles);
    }

    private function _updateDiff($data)
    {
        /** @var Billiondigital_Themler_Model_Export_Storage_Diff $diff */
        $diff = Mage::getModel('billionthemler/export_storage_diff', array('theme' => $this->_themeName));
        $diff->load();
        $updateData = array();
        foreach ($data as $f) {
            $resultFile = str_replace($this->_cfg->get('paths/export/result'), '', $f);
            $updateData[] = preg_replace('/[\/\\\]/', DS, $resultFile);
        }
        $diff->update($updateData);
        $diff->save();
    }

    public function buildPackage()
    {
        $basePath = MAGENTO_ROOT . $this->_cfg->get('paths/export/result');
        $package = Mage::getModel('billionthemler/export_package', array($basePath . '/package.xml'));
        $package->build($basePath);
        $package->save($basePath . '/package.xml');
    }

    /**
     * @param string $path
     */
    public function saveTo($path)
    {
        $this->_fso->copy(MAGENTO_ROOT . $this->_cfg->get('paths/export/result'), $path);
    }

    public function clear()
    {
        $this->_fso->remove(MAGENTO_ROOT . $this->_cfg->get('paths/export/temp'), true);
    }

}

//