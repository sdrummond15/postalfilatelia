<?php
//

class Billiondigital_Theme_Helper_Data extends Billiondigital_Core_Helper_Data
{
    public function getCaptureModel()
    {
        return Mage::getModel('billiontheme/buffer');
    }

    public function getMessagesHelper()
    {
        return Mage::helper('billiontheme/messages');
    }

    public function getThemlerHelper()
    {
        $helper = null;
        if (Mage::helper('core')->isModuleEnabled('Billiondigital_Themler')) {
            $helper = Mage::helper('billionthemler');
        }
        return $helper;
    }

    public function getProductHelper()
    {
        return Mage::helper('billiontheme/product');
    }

    /**
     * @param $name
     * @param array $blockData
     */
    public function getPositionHtml($name, $blockParams = array())
    {
        $xpathHelper = Mage::helper('billioncore/xpath');
        $result = '';
        $block = Mage::app()->getLayout()->getBlock($name);
        $titleName = 'block-title';
        $contentName = 'block-content';

        if ($block) {
            $output = $block->toHtml();
            $output = str_replace("\r", '', $output);
            $output = str_replace('&', 'ESCAPE_CHAR_AMP', $output);

            if ($output) {
                $output = mb_convert_encoding($output, 'HTML-ENTITIES', 'UTF-8');
                $dom = new Zend_Dom_Query($output);
                $blocks = $dom->query('.block');
                $doc = $blocks->getDocument();
                $xpath = new DOMXPath($doc);

                foreach ($blocks as $blockDom) {
                    if ($_nodeClass = $blockDom->getAttributeNode('class')) {
                        $_nodeClass->value .= ' ' . $blockParams['class'];
                    } else {
                        $blockDom->setAttribute('class', $blockParams['class']);
                    }
                    $title = $xpath->query('.//*[' . $xpathHelper->hasClassExpr('@class', $titleName) . ']', $blockDom);
                    $content = $xpath->query('.//*[' . $xpathHelper->hasClassExpr('@class', $contentName) . ']', $blockDom);
                    if ($title->length) {
                        $xpathHelper->removeClass($title, $titleName);
                        $xpathHelper->addClass($title, $blockParams['headerClass']);

                        $textContent = $title->item(0)->textContent;
                        while ($title->item(0)->hasChildNodes()) {
                            $title->item(0)->removeChild($title->item(0)->firstChild);
                        }
                        $h4 = $doc->createElement('h4');
                        $h4->nodeValue = $textContent;
                        $title->item(0)->appendChild($h4);
                    }
                    if ($content->length) {
                        $xpathHelper->removeClass($content, $contentName);
                        $xpathHelper->addClass($content, $blockParams['contentClass']);
                    }
                }

                $body = $xpath->query('//body');
                if ($body->length) {
                    foreach($doc->getElementsByTagName('script') as $script){
                        $script->nodeValue = str_replace(
                            array('<', '>'),
                            array('ESCAPE_CHAR_LT', 'ESCAPE_CHAR_GT'),
                            $script->nodeValue
                        );
                    }

                    foreach ($body->item(0)->childNodes as $child) {
                        $result .= $doc->saveXML($child, LIBXML_NOEMPTYTAG);
                    }

                    $result = str_replace(
                        array('ESCAPE_CHAR_LT', 'ESCAPE_CHAR_GT', 'ESCAPE_CHAR_AMP', 'class="button"', 'class=""'),
                        array('<', '>', '&'),
                        $result
                    );
                }
            }
        }

        return $result;
    }

    public function getPriceHtml($product)
    {
        $list = Mage::app()->getLayout()->getBlock('product_list');
        if (!$list) {
            $list = Mage::app()->getLayout()->createBlock('catalog/product_list');
        }
        return $list->getPriceHtml($product, true);
    }

    public function createTemplate($name, $controlId = null)
    {
        $tpl = new Billiondigital_Theme_Block_Template();
        $tpl->setTemplateType($name);
        if (!is_null($controlId)) {
            $tpl->setControlId($controlId);
        }
        return $tpl;
    }

    public function createSlider()
    {
        $tpl = $this->createTemplate('product_slider');
        $tpl->setLayout(Mage::app()->getLayout());
        return $tpl;
    }

    public function getSliderSettings($id)
    {
        $settings = array();
        $theme = $this->getThemeName();
        $data = unserialize($this->getConfigValue("designer/settings/$theme/slider", ''));

        if (is_array($data) && array_key_exists($id, $data) && is_array($data[$id])) {
            $settings = $data[$id];
            $settings['enabled'] = !array_key_exists('enabled', $settings) || $settings['enabled'];

            if (!array_key_exists('source', $settings) || !strlen($settings['source'])) {
                $settings['source'] = $this->getProductHelper()->CATEGORY;
            }

            if (empty($settings['perslide'])) {
                $settings['perslide'] = 4;
            }
        }

        return $settings;
    }

    public function setTemplateFallback($block)
    {
        $templateType = $block->getTemplateType();
        $controlId = $block->getControlId();
        $filename = $templateType . ($controlId !== null ? '_' . $controlId : '') . '.phtml';

        $params = array( '_type' => 'template', '_default' => false );
        if (Mage::getSingleton('core/design_package')->validateFile('designer/' . $filename, $params)) {
            $block->setTemplate('designer/' . $filename);
        } else if (Mage::getSingleton('core/design_package')->validateFile($filename, $params)) {
            $block->setTemplate($filename);
        }
    }

    /**
     * @param $category
     * @return bool
     */
    public function categoryIsActive($category)
    {
        $catalogLayer = Mage::getSingleton('catalog/layer');
        if (!$catalogLayer) {
            return false;
        }

        $currentCategory = $catalogLayer->getCurrentCategory();
        if (!$currentCategory) {
            return false;
        }

        $categoryPathIds = explode(',', $currentCategory->getPathInStore());
        return in_array($category->getId(), $categoryPathIds);
    }

    public function getSkinUrl($file, $params = array())
    {
        $baseName = Mage::getDesign()->getSkinBaseDir(array('_theme' => $this->getThemeName())) . DS . $file;
        if (file_exists($baseName)) {
            return Mage::getDesign()->getSkinUrl($file, array('_theme' => $this->getThemeName()));
        } else {
            return Mage::getDesign()->getSkinUrl($file, $params);
        }
    }

}

//