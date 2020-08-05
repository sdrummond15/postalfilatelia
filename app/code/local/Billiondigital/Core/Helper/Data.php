<?php
//

class Billiondigital_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getThemeVersion()
    {
        //
        return '1.0.72';
        //
    }

    /**
     * @param $themeName
     * @return bool
     */
    public function isActive($themeName)
    {
        $theme = Mage::getConfig()->getNode('default/design/theme/default');
        return $theme && $theme->asArray() == $themeName;
    }

    /**
     * @param $path
     * @param string $default
     * @return array|string
     */
    public function getConfigValue($path, $default = '')
    {
        $node = Mage::getConfig()->getNode($path, 'default');
        return $node ? $node->asArray() : $default;
    }

    public function getThemeName()
    {
        return preg_replace('/_preview$/', '', Mage::getSingleton('core/design_package')->getTheme('template'));
    }

    /**
     * @param $input
     * @param $columns
     * @return array
     */
    public function arrayColumns($input, $columns)
    {
        $result = array();
        if (!is_array($columns) || !count($columns)) return $result;
        foreach ($columns as $column) {
            $result[$column] = array();
        }

        foreach ($input as $item) {
            foreach ($columns as $column) {
                $result[$column][] = array_key_exists($column, $item) ? $item[$column] : '';
            }
        }
        return $result;
    }

    /**
     * @param $needle
     * @param $haystack
     * @return bool|int|string
     */
    public function arrayRecursiveSearch($needle, $haystack)
    {
        foreach($haystack as $key=>$value) {
            $current_key = $key;
            if($needle === $value OR (is_array($value) && $this->arrayRecursiveSearch($needle, $value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }
}

//