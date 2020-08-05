<?php
//

class Billiondigital_Themler_Model_Package_Info
{
    private $_packageInfo = array(
        "file_name" => "",
        "name" => "DesignerTheme",
        "channel" => "community",
        "version_ids" => array("2"),
        "summary" => "Billion Themler theme package",
        "description" => "Billion Themler theme package",
        "license" => "Billion Themler license",
        "license_uri" => "http://license_uri",
        "version" => "1.0.0.0",
        "stability" => "stable",
        "notes" => "",
        "authors" => array(
            "name" => array("Billion Themler"),
            "user" => array("Designer"),
            "email" => array("magento@theme.designer.net")
        ),
        "depends_php_min" => "5.2.0",
        "depends_php_max" => "6.0.0",
        "depends" => array(
            "package" => array(
                "name" => array(),
                "channel" => array(),
                "min" => array(),
                "max" => array(),
                "files" => array()
            ),
            "extension" => array(
                "name" => array(),
                "min" => array(),
                "max" => array()
            )
        ),
        "contents" => array(
            "target" => array('magelocal'),
            "path" => array(''),
            "type" => array(''),
            "include" => array(''),
            "ignore" => array('')
        )
    );

    public function setName($name)
    {
        $this->_packageInfo['name'] = $name;
    }

    public function addContent($target, $path, $type = 'dir', $include = '', $ignore = '')
    {
        $this->_packageInfo['contents']['target'][] = $target;
        $this->_packageInfo['contents']['path'][] = $path;
        $this->_packageInfo['contents']['type'][] = $type;
        $this->_packageInfo['contents']['include'][] = $include;
        $this->_packageInfo['contents']['ignore'][] = $ignore;
    }

    public function addDependPackage($name, $channel, $min, $max, $files)
    {
        $this->_packageInfo['depends']['package']['name'][] = $name;
        $this->_packageInfo['depends']['package']['channel'][] = $channel;
        $this->_packageInfo['depends']['package']['min'][] = $min;
        $this->_packageInfo['depends']['package']['max'][] = $max;
        $this->_packageInfo['depends']['package']['files'][] = $files;
    }

    public function addDependExtension($name, $min, $max)
    {
        $this->_packageInfo['depends']['extension']['name'][] = $name;
        $this->_packageInfo['depends']['extension']['min'][] = $min;
        $this->_packageInfo['depends']['extension']['max'][] = $max;
    }

    public function getData()
    {
        return $this->_packageInfo;
    }

    public function getArchivePath()
    {
        return Mage::helper('connect')->getLocalPackagesPath()
            . $this->_packageInfo['name']
            . '-'
            . $this->_packageInfo['version']
            . '.tgz';
    }
}

//