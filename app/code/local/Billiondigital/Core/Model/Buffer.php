<?php
//

class Billiondigital_Core_Model_Buffer extends Varien_Object
{
    private $_currentSection = '';
    private $_sections = null;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function capture($name)
    {
        if (is_null($this->_sections)) {
            $this->_sections = array();
            ob_start();
        } else if ($this->_currentSection) {
            $this->_sections[$this->_currentSection] = ob_get_clean();
            ob_start();
        }
        $this->_currentSection = $name;
        return true;
    }

    /**
     * @return array
     */
    public function complete()
    {
        $sections = array();
        if (!is_null($this->_sections)) {
            $sections = $this->_sections;
            $sections[$this->_currentSection] = ob_get_clean();
            $this->_currentSection = '';
            $this->_sections = null;
        }
        return array_merge($sections, parent::getData());
    }
}
//