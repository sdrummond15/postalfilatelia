<?php
//

class Billiondigital_Theme_Model_ThemeList extends Varien_Object
{
    public function getThemeList()
    {
        $appDir = MAGENTO_ROOT . Mage::getSingleton('billioncore/config')->get('paths/app_base');
        $themes = new Varien_Data_Collection();
        $dir = opendir($appDir);

        while (($item = readdir($dir)) !== false) {
            $themeDir = $appDir . DS . $item;
            if ($item === '.' || $item === '..' || preg_match('/_preview$/i', $item) ||
                !file_exists($themeDir . '/layout/local.xml')) continue;

            $layout = simplexml_load_file($themeDir . '/layout/local.xml');

            if (!$layout || !isset($layout['version']) || strlen((string) $layout['version']) !== 36)
                continue;

            $theme = Mage::getModel('billiontheme/theme');
            $theme->setData('name', $item);
            $theme->setData(
                'has_project',
                is_readable($themeDir . '/designer/project.json')
            );
            $theme->setData('has_themler', Mage::helper('core')->isModuleEnabled('Billiondigital_Themler'));
            $theme->setData('thumbnail_url', Mage::getDesign()->getSkinUrl('images/preview.png', array('_area' => 'frontend', '_package' => 'default', '_theme' => $item)));
            $themes->addItem($theme);
        }
        return $themes;
    }
}

//