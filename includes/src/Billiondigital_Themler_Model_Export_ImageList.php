<?php
//

class Billiondigital_Themler_Model_Export_ImageList
{
    /**
     * @var Billiondigital_Themler_Model_Export_Image[]
     */
    private $_images = array();
    private $_cssReplaceInfo = array();
    private $_htmlReplaceInfo = array();

    public function add($id, $content)
    {
        $image = Mage::getModel('billionthemler/export_image');
        $image->setId($id);
        $image->setContent($content);
        $image->save();
        $this->_images[$id] = $image;
    }

    public function export($exportDir)
    {
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0777, true);
        }

        foreach ($this->_images as $image) {
            $imagePath = $exportDir . '/' . $image->getFileName();
            $content = $image->getContent();
            if ($content) {
                file_put_contents($imagePath, $content === '[DELETED]' ? $content : base64_decode($content));
            }

            $this->_cssReplaceInfo[] = array(
                'from' => 'url(' . $image->getId() . ')',
                'to' => 'url(images/designer/' . $image->getFileName() . ')'
            );

            $this->_htmlReplaceInfo[] = array(
                'from' => 'url(' . $image->getId() . ')',
                'to' => '<?php echo Mage::registry(\'templateHelper\')->getSkinUrl(\'images/designer/' . $image->getFileName() . '\') ?>'
            );
        }
    }

    public function getCssReplaceInfo()
    {
        return Mage::helper('billioncore')->arrayColumns($this->_cssReplaceInfo, array('from', 'to'));
    }

    public function getHtmlReplaceInfo()
    {
        return Mage::helper('billioncore')->arrayColumns($this->_htmlReplaceInfo, array('from', 'to'));
    }
}

//