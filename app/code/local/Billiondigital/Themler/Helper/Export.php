<?php
//

class Billiondigital_Themler_Helper_Export extends Mage_Core_Helper_Abstract
{
    /**
     * @param $themeName
     * @return array
     */
    public function getHashes($themeName)
    {
        /** @var Billiondigital_Themler_Model_Export_Storage_Abstract $hashes */
        $hashes = Mage::getModel('billionthemler/export_storage_hashes', array(
            'theme' => $themeName,
            'basePath' => null
        ));
        $json = $hashes->load()->toJson();
        return $json ? $json : '{}';
    }

    /**
     * @param $themeName
     * @return mixed
     */
    public function getCache($themeName)
    {
        /** @var Billiondigital_Themler_Model_Export_Storage_Abstract $cache */
        $cache = Mage::getModel('billionthemler/export_storage_cache', array(
            'theme' => $themeName
        ));
        $json = $cache->load()->toJson();
        return $json ? $json : '{}';
    }

    public function unpackFso($fso, $path) {
        if(!is_array($fso['items'])) {
            return;
        }
        if(!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        if (!is_writable($path)) {
            throw new Billiondigital_Core_PermissionException('Permission denied: ' . $path);
        }
        foreach ($fso['items'] as $name => $file) {
            if(isset($file['content']) && isset($file['type'])) {
                switch ($file['type']) {
                    case 'text':
                        file_put_contents($path . DS . $name, $file['content']);
                        break;
                    case 'data':
                        file_put_contents($path . DS . $name, base64_decode($file['content']));
                        break;
                }
            } else if (isset($file['items']) && isset($file['type'])) {
                $this->unpackFso($file, $path . DS . $name);
            }
        }
    }

    public function packFso($path) {
        $result = array();

        if (is_file($path)) {
            $content = file_get_contents($path);

            if ($content === false) {
                throw new Billiondigital_Core_PermissionException('Permission denied: ' . $path);
            }

            $type = 'text';
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            if (in_array($ext, array('jpg', 'jpeg', 'bmp', 'png', 'gif', 'svg'))) {
                $type = 'data';
                $content = base64_encode($content);
            }

            $result = array('type' => $type, 'content' => $content);
        } else if (is_dir($path)) {
            $result = array('type' => 'dir', 'items' => array());

            if ($d = opendir($path)) {
                while (($name = readdir($d)) !== false) {
                    if (in_array($name, array('.', '..'))) {
                        continue;
                    }

                    $result['items'][$name] = $this->packFso($path . DS . $name);
                }
                closedir($d);
            }
        }

        return $result;
    }
}

//