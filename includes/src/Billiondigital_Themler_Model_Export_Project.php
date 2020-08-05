<?php
//

class Billiondigital_Themler_Model_Export_Project
{
    private $_name;
    // data
    private $_projectData;
    private $_thumbnails;

    private $_fso;
    private $_cfg;

    public function __construct($args)
    {
        list($name, $projectData) = $args;

        $this->_name = $name;
        $this->_projectData = isset($projectData['projectData']) ? $projectData['projectData'] : '';
        $this->_thumbnails = isset($projectData['thumbnails']) ? $projectData['thumbnails'] : array();

        $this->_fso = Mage::helper('billioncore/filesystem');
        $this->_cfg = Mage::getSingleton('billioncore/config');
    }

    public function serializeData()
    {
        return json_encode(array(
            'projectData' => $this->_projectData
        ));
    }

    public function getData()
    {
        return $this->_projectData;
    }

    /**
     * Saves designer user session data
     */
    public function save()
    {
        $saveData = array(
            'projectData' => $this->_projectData
        );

        $projectPath = self::getProjectPath($this->_name);
        $this->_fso->write($projectPath, json_encode($saveData));

        $thumbnailPath = MAGENTO_ROOT . $this->_cfg->get('paths/theme/thumbnails', array('theme' => $this->_name));
        foreach ($this->_thumbnails as $thumbnail) {
            $this->_fso->write(
                $thumbnailPath . DS . $thumbnail['name'],
                base64_decode(str_replace('data:image/png;base64,', '', $thumbnail['data']))
            );
        }
    }

    /**
     * @param string $name
     * @return Billiondigital_Themler_Model_Export_Project
     */
    public static function open($name = '')
    {
        if (!$name) {
            throw new Mage_Adminhtml_Exception('Name can\'t be empty');
        }
        $data = array();
        if (self::exists($name)) {
            $data = json_decode(file_get_contents(self::getProjectPath($name)), true);
        }
        return Mage::getModel('billionthemler/export_project', array($name, $data));
    }

    public static function exists($name = '')
    {
        return file_exists(self::getProjectPath($name));
    }

    public static function getProjectPath($themeName)
    {
        return MAGENTO_ROOT . Mage::getSingleton('billioncore/config')->get('paths/theme/project', array('theme' => $themeName));
    }
}

//