<?php
//

class Billiondigital_Theme_Helper_Product extends Mage_Core_Helper_Abstract
{
    public $BESTSELLERS = 1;
    public $MOSTVIEWED = 2;
    public $NEWPRODUCTS = 3;
    public $CATEGORY = 4;

    public $DEFAULT_PRODUCTS_COUNT = 10;

    public function getBestsellers($count = 0)
    {
        $products = array();

        $count = $count ? $count : $this->DEFAULT_PRODUCTS_COUNT;
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getResourceModel('sales/report_bestsellers_collection')
            ->setModel('catalog/product')
            ->addStoreFilter($storeId);

        $collection->setPageSize($count)->setCurPage(1)->load();

        foreach ($collection as $item) {
            $products[] = Mage::getModel('catalog/product')->setStoreId($storeId)->load($item->getProductId());
        }

        return $products;
    }

    public function getMostviewed($count = 0)
    {
        $count = $count ? $count : $this->DEFAULT_PRODUCTS_COUNT;
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addViewsCount();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $collection->setPageSize($count)->setCurPage(1);

        return $collection->load();
    }

    public function getNew($count = 0)
    {
        $count = $count ? $count : $this->DEFAULT_PRODUCTS_COUNT;
        $storeId = Mage::app()->getStore()->getId();

        $todayStartOfDayDate = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());


        $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addUrlRewrite()
            ->addStoreFilter($storeId)
            ->addAttributeToFilter('news_from_date', array('or'=> array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                    array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                    )
              )
            ->addAttributeToSort('news_from_date', 'desc')
            ->setPageSize($count)
            ->setCurPage(1)
        ;

        return $collection->load();
    }

    public function getProductsByCategory($category, $count = 0)
    {
        $collection = null;

        if (is_numeric($category)) {
            $count = $count ? $count : $this->DEFAULT_PRODUCTS_COUNT;

            $category = Mage::getModel('catalog/category')->load($category);

            $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addCategoryFilter($category)
                ->setPageSize($count)
                ->setCurPage(1);
            $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
            $collection->load();
        }

        return $collection;
    }

    public function getProducts($list) {
        $storeId = Mage::app()->getStore()->getId();
        $collection = array();
        $products = explode(',', $list);

        if (count($products)) {
            foreach ($products as $productId) {
                $collection[] = Mage::getModel('catalog/product')->setStoreId($storeId)->load(trim($productId));
            }
        }

        return $collection;
    }
}

//