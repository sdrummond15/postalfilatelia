<?php
//

class Billiondigital_Themler_Model_Export_Storage_Hashes extends Billiondigital_Themler_Model_Export_Storage_Abstract
{
    public function __construct($args)
    {
        parent::__construct('paths/theme/hashes', $args);
    }

    protected function _update($hashes)
    {
        foreach ($hashes as $file => $hash) {
            if ('[DELETED]' === $hash) {
                if (isset($this->data[$file])) {
                    unset($this->data[$file]);
                }
                continue;
            } else {
                $this->data[$file] = $hash;
            }
        }
    }
}

//