<?php
//

class Billiondigital_Themler_Model_Export_Storage_PreviewCode extends Billiondigital_Themler_Model_Export_Storage_Abstract
{
    public function __construct($args)
    {
        parent::__construct('paths/theme/previewCode', $args);
    }

    protected function _update($data)
    {
        if (!is_array($data)) return;

        $this->data = $data;
    }
}

//