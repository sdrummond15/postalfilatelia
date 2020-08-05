<?php
//

class Billiondigital_Themler_Model_Export_FontList
{
    private $_fonts = array();
    private $_cssReplaceInfo = array();

    public function add($id, $content)
    {
        $font = new Varien_Object();
        $font->setId($id);
        $font->setContent(preg_replace('/^[^,]+,/', '', $content));
        $this->_fonts[$id] = $font;
    }

    public function export($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        foreach ($this->_fonts as $font) {
            $fontPath = $dir . '/' . $font->getId();
            file_put_contents($fontPath, base64_decode($font->getContent()));

            $this->_cssReplaceInfo[] = array(
                'from' => $font->getId(),
                'to' => $font->getId()
            );
        }
    }

    public function getCssReplaceInfo()
    {
        return Mage::helper('billioncore')->arrayColumns($this->_cssReplaceInfo, array('from', 'to'));
    }
}

//