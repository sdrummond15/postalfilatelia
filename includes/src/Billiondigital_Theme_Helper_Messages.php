<?php
//

class Billiondigital_Theme_Helper_Messages extends Mage_Core_Helper_Abstract
{
    private function _factory($type, $content, $escape = true)
    {
        return Mage::helper('billiontheme')->createTemplate($type)
            ->setMessages(is_array($content) ? $content : array($escape ? $this->escapeHtml($content) : $content));
    }

    public function error($content, $escape = true)
    {
        echo $this->_factory(Mage_Core_Model_Message::ERROR, $content, $escape)->toHtml();
    }

    public function warning($content, $escape = true)
    {
        echo $this->_factory(Mage_Core_Model_Message::WARNING, $content, $escape)->toHtml();
    }

    public function notice($content, $escape = true)
    {
        echo $this->_factory(Mage_Core_Model_Message::NOTICE, $content, $escape)->toHtml();
    }

    public function success($content, $escape = true)
    {
        echo $this->_factory(Mage_Core_Model_Message::SUCCESS, $content, $escape)->toHtml();
    }

    public function all($messages = array(), $escape = true)
    {
        $html = '';
        $types = array(
            Mage_Core_Model_Message::ERROR,
            Mage_Core_Model_Message::WARNING,
            Mage_Core_Model_Message::NOTICE,
            Mage_Core_Model_Message::SUCCESS
        );

        if ($messages) {
            foreach ($types as $type) {
                $contents = array();
                foreach ($messages as $message) {
                    if ($type !== $message->getType()) continue;
                    $contents[] = $message->getText();
                }
                $html .= $this->_factory($type, $contents, $escape)->toHtml();
            }
        }

        return $html;
    }
}

//