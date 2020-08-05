<?php
//

class Billiondigital_Themler_Model_Export_Storage_Diff extends Billiondigital_Themler_Model_Export_Storage_Abstract
{
    public function __construct($args)
    {
        parent::__construct('paths/theme/diff', $args);
    }

    protected function _update($data)
    {
        $this->data = array_unique(array_merge($this->data, $data));
    }
}

//