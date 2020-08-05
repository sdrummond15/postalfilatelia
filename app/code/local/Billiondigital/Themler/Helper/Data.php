<?php
//

class Billiondigital_Themler_Helper_Data extends Billiondigital_Core_Helper_Data
{
    public function isPreview()
    {
        return preg_match('/_preview$/', Mage::getSingleton('core/design_package')->getTheme('template'));
    }

    /**
     * @param $crumbs
     * @return array
     */
    public function fixPreviewBreadcrumbs($crumbs)
    {
        $url = Mage::helper('core/url');
        $fixedCrumbs = array();

        foreach ($crumbs as $crumbName => $crumbInfo) {
            $link = $url->removeRequestParam($crumbInfo['link'], 'theme');
            $link = $url->removeRequestParam($link, 'preview');
            $link = $url->addRequestParam($link, array(
                'theme' => Mage::app()->getRequest()->getParam('theme'),
                'preview' => Mage::app()->getRequest()->getParam('preview')
            ));
            $crumbInfo['link'] = $link;
            $fixedCrumbs[$crumbName] = $crumbInfo;
        }

        return $fixedCrumbs;
    }

    public function getThemesInfo()
    {
        $list = array();

        foreach (Mage::getModel('billiontheme/themeList')->getThemeList() as $theme) {
            if ($theme->getHasProject()) {
                $name = $theme->getName();
                $list[$name] = array(
                    'themeName' => $name,
                    'thumbnailUrl' => $theme->getThumbnailUrl(),
                    'openUrl' => $theme->getEditUrl(),
                    'isActive' => $this->isActive($name)
                );
            }
        }

        return $list;
    }
}

//