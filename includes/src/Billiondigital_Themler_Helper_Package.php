<?php
//

class Billiondigital_Themler_Helper_Package extends Mage_Core_Helper_Abstract
{
    public function createPackage($sourceTheme, $targetTheme, $includeEditor)
    {
        $cfg = Mage::getSingleton('billioncore/config');
        $previewDirname = $cfg->get('paths/preview/dirname', array('theme' => $sourceTheme));
        $themeDirname = $cfg->get('paths/theme/dirname', array('theme' => $sourceTheme));
        if (!$targetTheme) {
            $targetTheme = $sourceTheme;
        }

        $info = Mage::getModel('billionthemler/package_info');
        $info->setName($targetTheme);

        $info->addContent('magedesign', 'frontend/default' . $themeDirname . '/etc'); // base theme
        $info->addContent('magedesign', 'frontend/default' . $themeDirname . '/layout/local.xml', 'file'); // base theme
        $info->addContent('magedesign', 'frontend/default' . $themeDirname . '/template'); // base theme
        $info->addContent('mageskin', 'frontend/default' . $themeDirname); // base theme

        if ($includeEditor) {
            $info->addContent('magedesign', 'frontend/default' . $themeDirname . '/designer'); // base theme
            $info->addContent('magedesign', 'frontend/default' . $themeDirname . '/layout/billionthemler.xml', 'file'); // base theme

            $info->addContent('magedesign', 'frontend/default' . $previewDirname); // preview theme
            $info->addContent('mageskin', 'frontend/default' . $previewDirname); // preview theme

            $info->addContent('magelocal', 'Billiondigital/Themler'); // Theme modules
            $info->addContent('mageetc', 'modules/Billiondigital_Themler.xml', 'file'); // Theme/Designer xml loader
            $info->addContent('magedesign', 'adminhtml/default/default/layout/billionthemler.xml', 'file'); // Adminhtml design
            $info->addContent('magedesign', 'adminhtml/default/default/template/billionthemler'); // Adminhtml design
            $info->addContent('mageskin', 'adminhtml/default/default/billionthemler'); // Adminhtml skin
        }

        $info->addContent('magelocal', 'Billiondigital/Core'); // Theme modules
        $info->addContent('magelocal', 'Billiondigital/Theme'); // Theme modules
        $info->addContent('mageetc', 'modules/Billiondigital_Core.xml', 'file'); // Theme/Designer xml loader
        $info->addContent('mageetc', 'modules/Billiondigital_Theme.xml', 'file'); // Theme/Designer xml loader
        $info->addContent('magedesign', 'adminhtml/default/default/layout/billiontheme.xml', 'file'); // Adminhtml design
        $info->addContent('magedesign', 'adminhtml/default/default/template/billioncore'); // Adminhtml design
        $info->addContent('magedesign', 'adminhtml/default/default/template/billiontheme'); // Adminhtml design
        $info->addContent('mageskin', 'adminhtml/default/default/billiontheme'); // Adminhtml skin

        $ext = Mage::getModel('billionthemler/package_extension', array('themeName' => $targetTheme));
        $ext->setData($info->getData());
        $ext->createPackage();

        $archivePath = $info->getArchivePath();
        $packageData = '';

        if (is_readable($archivePath)) {
            $packageData = file_get_contents($archivePath);
            unlink($archivePath);
        }

        return $packageData;
    }

    public function unpackPackage($uploadPath)
    {
        $fso = Mage::helper('billioncore/filesystem');

        $error = null;
        $extractPath = $uploadPath . '_content';
        $fso->mkdir($extractPath, 0777, true);

        $archiver = new Mage_Archive();
        try {
            $archiver->unpack($uploadPath, $extractPath);
        } catch (Exception $e) {
            $fso->remove($uploadPath);
            $fso->remove($extractPath, true);
            return 'Unpack theme error';
        }

        $currentCoreVersion = Mage::getConfig()->getModuleConfig('Billiondigital_Core')->version;
        $currentThemeVersion = Mage::getConfig()->getModuleConfig('Billiondigital_Theme')->version;
        $currentThemlerVersion = Mage::getConfig()->getModuleConfig('Billiondigital_Themler')->version;

        $coreConfigPath = $extractPath . '/app/code/local/Billiondigital/Core/etc/config.xml';
        $themeConfigPath = $extractPath . '/app/code/local/Billiondigital/Theme/etc/config.xml';
        $themlerConfigPath = $extractPath . '/app/code/local/Billiondigital/Themler/etc/config.xml';

        libxml_use_internal_errors(true); // disable xml errors

        $coreConfig = simplexml_load_file($coreConfigPath);
        $themeConfig = simplexml_load_file($themeConfigPath);
        $themlerConfig = is_readable($themlerConfigPath) ? simplexml_load_file($themlerConfigPath) : null;

        $errors = libxml_get_errors();

        if (count($errors)) {
            $error = 'Incorrect theme version or format';
        } else {
            $newCoreVersion = $coreConfig->modules->Billiondigital_Core->version;
            $newThemeVersion = $themeConfig->modules->Billiondigital_Theme->version;

            if (version_compare($newCoreVersion, $currentCoreVersion, '>')) {
                $fso->copy(
                    $extractPath . '/app/code/local/Billiondigital/Core',
                    MAGENTO_ROOT . '/app/code/local/Billiondigital/Core'
                );
                $fso->copy(
                    $extractPath . '/app/design/adminhtml/default/default/template/billioncore',
                    MAGENTO_ROOT . '/app/design/adminhtml/default/default/template/billioncore'
                );
            }

            if (version_compare($newThemeVersion, $currentThemeVersion, '>')) {
                $fso->copy(
                    $extractPath . '/app/code/local/Billiondigital/Theme',
                    MAGENTO_ROOT . '/app/code/local/Billiondigital/Theme'
                );
                $fso->copy(
                    $extractPath . '/app/design/adminhtml/default/default/template/billiontheme',
                    MAGENTO_ROOT . '/app/design/adminhtml/default/default/template/billiontheme'
                );
                $fso->copy(
                    $extractPath . '/app/design/adminhtml/default/default/layout/billiontheme.xml',
                    MAGENTO_ROOT . '/app/design/adminhtml/default/default/layout/billiontheme.xml'
                );
                $fso->copy(
                    $extractPath . '/skin/adminhtml/default/default/billiontheme',
                    MAGENTO_ROOT . '/skin/adminhtml/default/default/billiontheme'
                );
            }

            if ($themlerConfig) {
                $newThemlerVersion = $themlerConfig->modules->Billiondigital_Themler->version;
                if (version_compare($newThemlerVersion, $currentThemlerVersion, '>')) {
                    $fso->copy(
                        $extractPath . '/app/code/local/Billiondigital/Themler',
                        MAGENTO_ROOT . '/app/code/local/Billiondigital/Themler'
                    );
                    $fso->copy(
                        $extractPath . '/app/design/adminhtml/default/default/template/billionthemler',
                        MAGENTO_ROOT . '/app/design/adminhtml/default/default/template/billionthemler'
                    );
                    $fso->copy(
                        $extractPath . '/app/design/adminhtml/default/default/layout/billionthemler.xml',
                        MAGENTO_ROOT . '/app/design/adminhtml/default/default/layout/billionthemler.xml'
                    );
                    $fso->copy(
                        $extractPath . '/skin/adminhtml/default/default/billionthemler',
                        MAGENTO_ROOT . '/skin/adminhtml/default/default/billionthemler'
                    );
                }
            }

            $fso->copy($extractPath . '/app/etc', MAGENTO_ROOT . '/app/etc');

            $themes = $fso->enumerate($extractPath . '/app/design/frontend/default', false, null, true);

            foreach ($themes as $dir) {
                if (!preg_match('#_preview$#', $dir) && is_dir($dir)) {
                    $themeName = basename($dir);
                    $newThemeName = $this->getAvailableThemeName($themeName);

                    // app
                    $fso->copy(
                        $dir,
                        MAGENTO_ROOT . '/app/design/frontend/default/' . $newThemeName
                    );

                    if (is_dir($dir . '_preview')) {
                        $fso->copy(
                            $dir . '_preview',
                            MAGENTO_ROOT . '/app/design/frontend/default/' . $newThemeName . '_preview'
                        );
                    }

                    // skin
                    if (is_dir($extractPath . '/skin/frontend/default/' . $themeName)) {
                        $fso->copy(
                            $extractPath . '/skin/frontend/default/' . $themeName,
                            MAGENTO_ROOT . '/skin/frontend/default/' . $newThemeName
                        );
                    }

                    if (is_dir($extractPath . '/skin/frontend/default/' . $themeName . '_preview')) {
                        $fso->copy(
                            $extractPath . '/skin/frontend/default/' . $themeName . '_preview',
                            MAGENTO_ROOT . '/skin/frontend/default/' . $newThemeName . '_preview'
                        );
                    }
                }
            }
        }

        libxml_clear_errors();

        $fso->remove($uploadPath);
        $fso->remove($extractPath, true);

        return $error;
    }

    public function getAvailableThemeName($themeName)
    {
        $path = Mage::helper('billioncore/path');

        while (file_exists($path->themeAppDir($themeName))) {
            preg_match('#(.*?)(\d{0,4})$#', $themeName, $m);
            $themeName = $m[1];
            $suffix = (int) $m[2];
            $suffix++;
            $themeName .= $suffix;
        };

        return $themeName;
    }
}

//