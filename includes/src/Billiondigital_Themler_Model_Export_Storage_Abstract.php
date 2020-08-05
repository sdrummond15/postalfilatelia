<?php
//

abstract class Billiondigital_Themler_Model_Export_Storage_Abstract
{
    private $_path = '';

    protected $fso;
    protected $cfg;

    protected $data = array();
    protected $content = '';

    public function __construct($path, $args)
    {
        $this->fso = Mage::helper('billioncore/filesystem');
        $this->cfg = Mage::getSingleton('billioncore/config');
        $this->_path = MAGENTO_ROOT . $this->cfg->get($path, $args);
    }

    public function toJson()
    {
        if ($this->data) {
            $this->content = json_encode($this->data);
        }
        return $this->content;
    }

    public function toArray()
    {
        if (!$this->data && $this->content)
            $this->data = json_decode($this->content, true);
        return $this->data;
    }

    public function save()
    {
        $this->fso->write($this->_path, $this->toJson());
    }

    public function remove()
    {
        if (!file_exists($this->_path)) return;
        $this->fso->remove($this->_path);
    }

    final public function update($data)
    {
        $this->toArray();
        $this->_update($data);
        return $this;
    }

    abstract protected function _update($data);

    public function load()
    {
        $this->content = file_exists($this->_path) ? file_get_contents($this->_path) : '';
        return $this;
    }
}

//