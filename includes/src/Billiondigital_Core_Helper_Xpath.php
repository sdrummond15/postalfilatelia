<?php
//

class Billiondigital_Core_Helper_Xpath extends Mage_Core_Helper_Abstract
{
    public function hasClassExpr($attr, $value)
    {
        return 'contains(concat(" ", normalize-space(' . $attr . '), " "), " ' . $value . ' ")';
    }

    public function addClass($item, $class)
    {
        $attr = $item->item(0)->getAttribute('class');
        $attr .= (strlen($attr) ? ' ' : '') . $class;
        $item->item(0)->setAttribute('class', $attr);
    }

    public function removeClass($item, $class)
    {
        $attr = $item->item(0)->getAttribute('class');
        $attr = str_replace(' ' . $class . ' ', '', ' ' . $attr . ' ');
        $item->item(0)->setAttribute('class', $attr);
    }
}

//