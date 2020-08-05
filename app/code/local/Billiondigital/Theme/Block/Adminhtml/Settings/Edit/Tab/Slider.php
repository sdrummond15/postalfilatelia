<?php
//

class Billiondigital_Theme_Block_Adminhtml_Settings_Edit_Tab_Slider extends Mage_Adminhtml_Block_Widget_Form
{
    private $_idx = 0;
    private $_staticSliders = array('Upsell', 'Crosssell', 'Related');

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $defaults = array();
        $this->_generateStatic($form, $defaults);
        $this->_generateDynamic($form, $defaults);

        $this->_fillForm($form, $defaults);

        return parent::_prepareForm();
    }

    private function _getFieldName($name, $id)
    {
        return 'slider_' . $name . '_' . $id;
    }

    private function _idx()
    {
        return $this->_idx++;
    }

    private function _fillForm($form, $data)
    {
        $model = Mage::getSingleton('Billiondigital_Theme_Model_Settings_Slider');
        $model->readOptions(Mage::registry('designer_settings_theme'));
        $modelData = $model->toArray();

        if (isset($modelData['slider']) && is_array($modelData['slider'])) {
            foreach ($modelData['slider'] as $modelDataId => $modelDataInfo) {
                if (is_array($modelDataInfo)) {
                    foreach ($modelDataInfo as $sliderOption => $optionValue) {
                        $data[$this->_getFieldName($sliderOption, $modelDataId)] = $optionValue;
                    }
                }
            }
        }

        $form->setValues($data);
    }

    private function _generateStatic($form, & $defaultValues)
    {
        foreach ($this->_staticSliders as $_staticId) {
            $fs = $form->addFieldset('form_slider' . $_staticId, array('legend' => $this->__('Slider ' . $_staticId)));
            $this->_generateBaseFields($_staticId, $fs, $defaultValues);
        }
    }

    private function _generateDynamic($form, & $defaultValues)
    {
        $sliderRegex = '#featured-slider_(\d+)\.phtml$#';

        $templatePath = Mage::getSingleton('core/design_package')->getBaseDir(
           array('_area' => 'frontend', '_type' => 'template', '_default' => false, '_theme' => Mage::registry('designer_settings_theme'))
        ) . '/designer';
        $sliders = Mage::helper('billioncore/filesystem')->enumerate($templatePath, false, $sliderRegex);

        if (count($sliders)) {
            foreach ($sliders as $slider) {
                $match = array();
                preg_match($sliderRegex, $slider, $match);
                if (!isset($match[1])) continue;
                $id = $match[1];

                $fs = $form->addFieldset('form_slider' . $id, array('legend' => $this->__('Slider ' . $id)));

                $enabledField = $this->_getFieldName('enabled', $id);
                $fs->addField($enabledField, 'select', array(
                    'label' => $this->__('Enabled'),
                    'name' => $enabledField,
                    'values' => array('1' => 'Yes', '0' => 'No'),
                    'tabindex' => $this->_idx(),
                    'required' => true,
                    'onchange' => 'enableSliderChange(' . $id . ')'
                ));
                $defaultValues[$enabledField] = '1';

                $sourceField = $this->_getFieldName('source', $id);
                $fs->addField($sourceField, 'select', array(
                    'label' => $this->__('Source'),
                    'name' => $sourceField,
                    'values' => array(
                        Mage::helper('billiontheme/product')->BESTSELLERS => 'Bestsellers',
                        Mage::helper('billiontheme/product')->MOSTVIEWED => 'Most Viewed',
                        Mage::helper('billiontheme/product')->NEWPRODUCTS => 'New',
                        Mage::helper('billiontheme/product')->CATEGORY => 'Category'
                    ),
                    'required' => true,
                    'tabindex' => $this->_idx(),
                    'onchange' => 'changeSliderDataSource(' . $id . ')',
                    'after_element_html' => '<script>jQuery(function () { changeSliderDataSource(' . $id . '); })</script>'
                ));
                $defaultValues[$sourceField] = Mage::helper('billiontheme/product')->CATEGORY;

                $productsField = $this->_getFieldName('products', $id);
                $fs->addField($productsField, 'text', array(
                    'label' => $this->__('Products'),
                    'name' => $productsField,
                    'tabindex' => $this->_idx(),
                    'after_element_html' => '<small style="display: block">If comma-separated ID List of Products is not set, products of Top Sellers will be displayed automatically, however, the slider will be hidden if there are no sales.</small>'
                ));
                $defaultValues[$productsField] = '';

                $categoryField = $this->_getFieldName('category', $id);
                $fs->addField($categoryField, 'text', array(
                    'label' => $this->__('Category'),
                    'name' => $categoryField,
                    'tabindex' => $this->_idx()
                ));
                $defaultValues[$categoryField] = '';

                $countField = $this->_getFieldName('count', $id);
                $fs->addField($countField, 'text', array(
                    'label' => $this->__('Products count'),
                    'name' => $countField,
                    'tabindex' => $this->_idx()
                ));
                $defaultValues[$countField] = '';

                $this->_generateBaseFields($id, $fs, $defaultValues);
            }
        }
    }

    private function _generateBaseFields($id, $fs, & $defaultValues)
    {
        $headerField = $this->_getFieldName('header', $id);
        $fs->addField($headerField, 'text', array(
            'label' => $this->__('Header'),
            'name' => $headerField,
            'tabindex' => $this->_idx()
        ));
        $defaultValues[$headerField] = $this->__('Products');

        $productsPerSlideField = $this->_getFieldName('perslide', $id);
        $fs->addField($productsPerSlideField, 'text', array(
            'label' => $this->__('Products to slide'),
            'name' => $productsPerSlideField,
            'tabindex' => $this->_idx()
        ));
        $defaultValues[$productsPerSlideField] = '4';

        // responsive

        $fs->addField($this->_getFieldName('responsiveLabel', $id), 'label', array(
            'after_element_html' => '<b>Columns</b>'
        ));

        $desktopWidthField = $this->_getFieldName('lg', $id);
        $fs->addField($desktopWidthField, 'select', array(
            'label'     => $this->__('Desktops'),
            'name'      => $desktopWidthField,
            'values' => array(
                '' => '--Please Select--',
                '24' => '1',
                '12' => '2',
                '8' => '3',
                '6' => '4',
                '4' => '6',
                '3' => '8'
            ),
            'tabindex' => $this->_idx()
        ));
        $defaultValues[$desktopWidthField] = '';

        $laptopWidthField = $this->_getFieldName('md', $id);
        $fs->addField($laptopWidthField, 'select', array(
            'label' => $this->__('Laptops'),
            'name' => $laptopWidthField,
            'values' => array(
                '' => '--Please Select--',
                '24' => '1',
                '12' => '2',
                '8' => '3',
                '6' => '4',
                '4' => '6',
                '3' => '8'
            ),
            'tabindex' => $this->_idx()
        ));
        $defaultValues[$laptopWidthField] = '';

        $tabletWidthField = $this->_getFieldName('sm', $id);
        $fs->addField($tabletWidthField, 'select', array(
            'label' => $this->__('Tablets'),
            'name' => $tabletWidthField,
            'values' => array(
                '' => '--Please Select--',
                '24' => '1',
                '12' => '2',
                '8' => '3',
                '6' => '4',
                '4' => '6',
                '3' => '8'
            ),
            'tabindex' => $this->_idx()
        ));
        $defaultValues[$tabletWidthField] = '6';

        $phoneWidthField = $this->_getFieldName('xs', $id);
        $fs->addField($phoneWidthField, 'select', array(
            'label' => $this->__('Phones'),
            'name' => $phoneWidthField,
            'values' => array(
                '' => '--Please Select--',
                '24' => '1',
                '12' => '2',
                '8' => '3',
                '6' => '4',
                '4' => '6',
                '3' => '8'
            ),
            'tabindex' => $this->_idx()
        ));
        $defaultValues[$phoneWidthField] = '';
    }
}

//