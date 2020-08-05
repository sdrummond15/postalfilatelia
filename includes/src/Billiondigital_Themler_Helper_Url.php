<?php
//

class Billiondigital_Themler_Helper_Url extends Mage_Core_Helper_Abstract
{
    private $_previewParams = array('theme' => '');

    private function _getTheme()
    {
        return $theme = Mage::app()->getRequest()->getParam('theme');
    }

    private function _getPreviewParams()
    {
        if (!$this->_previewParams || $this->_previewParams['theme'] !== $this->_getTheme()) {
            $this->_previewParams = array(
                'theme' => $this->_getTheme(),
                'preview' => 1,
                '_store' => Mage::app()->getDefaultStoreView()->getId()
            );
        }
        return $this->_previewParams;
    }

    public function getDesignerUrl($route, $params = array())
    {
        $url = Mage::helper('adminhtml')->getUrl($route);

        $requiredParams = array('theme' => $this->_getTheme());

        return Mage::helper('core/url')->addRequestParam($url, array_merge($requiredParams, $params));
    }

    public function getDesignerAjaxUrl($route, $params = array())
    {
        return $this->getDesignerUrl($route, array_merge($params, array('ajax' => 1)));
    }

    public function getPreviewUrl($route = 'cms/index/index', $params = array())
    {
        Mage::register('disableThemlerParams', true);
        $url = Mage::helper('core/url')->addRequestParam(Mage::getUrl($route), array_merge($this->_getPreviewParams(), $params));
        Mage::unregister('disableThemlerParams');
        return $url;
    }

    public function getCategoryPreviewUrl()
    {
        $url = Mage::getUrl('no/route/path');
        $rootId = Mage::app()->getWebsite(true)->getDefaultStore()->getRootCategoryId();
        $children = Mage::getModel('catalog/category')->load($rootId)->getAllChildren(true);

        if (count($children) > 1) {
            foreach ($children as $child) {
                if ($child === $rootId) continue;

                $category = Mage::getModel('catalog/category')->load($child);

                if ($category->getDisplayMode() === Mage_Catalog_Model_Category::DM_PAGE) {
                    continue;
                }

                $collection = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                    ->addFieldToFilter(
                        'visibility',
                        array(
                            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                        )
                    )
                    ->addCategoryFilter($category)
                    ->addStoreFilter(Mage::app()->getDefaultStoreView()->getId())
                    ->load();
                if ($collection->count()) {
                    $url = Mage::helper('catalog/category')->getCategoryUrl($category);
                    break;
                }
            }
        }

        $url = Mage::helper('core/url')->addRequestParam($url, $this->_getPreviewParams());

        return $url;
    }

    public function getProductPreviewUrl()
    {
        $url = Mage::getUrl('no/route/path');

        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->addFieldToFilter(
                'visibility',
                array(
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                    Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                )
            )
            ->addAttributeToSelect(array('url'))
            ->addStoreFilter(Mage::app()->getDefaultStoreView()->getId())
            ->load();

        if ($collection->count() && ($item = $collection->getFirstItem())) {
            $url = Mage::helper('core/url')->addRequestParam($item->getProductUrl(), $this->_getPreviewParams());
        }

        return $url;
    }

    public function getPagePreviewUrl()
    {
        $url = Mage::getUrl('no/route/path');

        $collection = Mage::getModel('cms/page')->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter(
                'identifier',
                array(
                    array('nin' => array('home', 'no-route', 'enable-cookies'))
                )
            )
            ->addStoreFilter(Mage::app()->getDefaultStoreView()->getId())
            ->load();

        if ($collection->count() && ($item = $collection->getFirstItem())) {
            $url = $this->getPreviewUrl('cms/page/view', array('page_id' => $item->getId()));
        }

        return $url;
    }
}

//