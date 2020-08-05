<?php
//

class Billiondigital_Theme_Block_Adminhtml_Settings_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'billiontheme';
        $this->_controller = 'adminhtml_settings';

        $this->_updateButton('save', 'label', $this->__('Save'));
        $this->_updateButton('delete', 'label', $this->__('Delete'));

        $this->_formScripts[] = '
            function enableSliderChange(id) {
                var selected = jQuery("#slider_enabled_" + id).find("option:selected");
                var enabled = selected.val() === "1"
                jQuery("#slider_header_" + id).prop("disabled", !enabled);
                jQuery("#slider_category_" + id).prop("disabled", !enabled);
                jQuery("#slider_count_" + id).prop("disabled", !enabled);
                jQuery("#slider_source_" + id).prop("disabled", !enabled);
                jQuery("#slider_products_" + id).prop("disabled", !enabled);
            }

            function changeSliderDataSource(id) {
                var sourceFields = {
                    ' . Mage::helper('billiontheme/product')->CATEGORY . ': [
                        "category"
                    ],
                    ' . Mage::helper('billiontheme/product')->BESTSELLERS . ': [
                        "products"
                    ]
                };

                // hide all
                for (var i in sourceFields) {
                    if (Array.isArray(sourceFields[i])) {
                        _changeVisible(sourceFields[i], "hide", id);
                    }
                }

                var selected = jQuery("#slider_source_" + id).find("option:selected").val();
                // show selected
                if (sourceFields[selected] && Array.isArray(sourceFields[selected])) {
                    _changeVisible(sourceFields[selected], "show", id);
                }
            }

            function _changeVisible(fields, action, id) {
                fields.forEach(function (field) {
                    jQuery("#slider_" + field + "_" + id).parents("tr")[action]();
                });
            }
        ';
    }

    public function getHeaderText()
    {
        return Mage::registry('designer_settings_theme') . ' ' . $this->__('theme');
    }
}

//