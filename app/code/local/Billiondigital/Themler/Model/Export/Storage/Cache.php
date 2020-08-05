<?php
//

class Billiondigital_Themler_Model_Export_Storage_Cache extends Billiondigital_Themler_Model_Export_Storage_Abstract
{
    public function __construct($args)
    {
        parent::__construct('paths/theme/cache', $args);
    }

    protected function _update($data)
    {
        foreach ($data as $control => $files) {
            if (!is_array($files)) continue;
            foreach ($files as $filename => $content) {
                if ($content === '[DELETED]') {
                    $this->_removeFile($control, $filename);
                } else {
                    $this->_addFile($control, $filename, $content);
                }
            }
        }
    }

    private function _removeFile($control, $filename)
    {
        if (!isset($this->data[$control]) || !isset($this->data[$control][$filename])) return;
        unset($this->data[$control][$filename]);
    }

    private function _addFile($control, $filename, $content)
    {
        $this->data[$control][$filename] = $content;
    }

}

//