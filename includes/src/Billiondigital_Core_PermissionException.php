<?php
// 

class Billiondigital_Core_PermissionException extends Zend_Exception
{
    public static $themeFolders = array(
        '/app/code/local/',
        '/app/etc/',
        '/app/design/frontend/default/',
        '/skin/frontend/default/',
        '/var/'
    );

    public function getHtmlMessage()
    {
        $html = Mage::app()->getLayout()
            ->createBlock('adminhtml/template')
            ->setDirs(self::$themeFolders)
            ->setTemplate('billioncore/permission-denied.phtml')
            ->toHtml();
        return '<p>' . $this->getMessage() . '</p>' . preg_replace('/[\r\n]/', '', $html);
    }
}

//