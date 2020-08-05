<?php
//

class Billiondigital_Themler_Model_Export_Layout
{
    private $_doc;
    private $_xpath;

    public function __construct($args)
    {
        list($xml) = $args;
        $this->_doc = new DOMDocument();
        $this->_doc->load($xml);
        $this->_xpath = new DOMXPath($this->_doc);
    }

    public function select($xpath)
    {
        return $this->_xpath->query($xpath);
    }

    /**
     * @param DOMElement $node
     * @param string $type
     * @param string $name
     * @param string $params
     * @param string $if
     */
    public function addItem($node, $type, $name, $params = '', $if = '')
    {
        $actionNode = new DOMElement('action');
        $node->appendChild($actionNode);
        $actionNode->setAttribute('method', 'addItem');
        $actionNode->appendChild(new DOMElement('type', $type));
        $actionNode->appendChild(new DOMElement('name', $name));
        if ($params)
            $actionNode->appendChild(new DOMElement('params', $params));
        if ($if)
            $actionNode->appendChild(new DOMElement('if', $if));
    }

    public function save($path)
    {
        return $this->_doc->save($path);
    }

}

//