<?php
//

class Billiondigital_Themler_Model_Export_Chunk
{
    private $_lastChunk = null;
    private $_chunkFolder = '';
    private $_lockFile = '';
    private $_isLast = false;

    public function save($info) {
        $this->validate($info);

        $this->_lastChunk = $info;
        $this->_chunkFolder = MAGENTO_ROOT . Mage::getSingleton('billioncore/config')->get('paths/export/upload') . DS . $info['id'];
        $this->_lockFile = $this->_chunkFolder . '/lock';

        if (!is_dir($this->_chunkFolder)) {
            Mage::helper('billioncore/filesystem')->mkdir($this->_chunkFolder, 0777, true);
        }

        if (!Mage::helper('billioncore/filesystem')->checkDirPermissions($this->_chunkFolder)) {
            throw new Billiondigital_Core_PermissionException('Incorrect permissions for ' . $this->_chunkFolder);
        } else {
            $f = fopen($this->_lockFile, 'c');

            if (flock($f, LOCK_EX)) {
                $chunks = array_diff(scandir($this->_chunkFolder), array('.', '..', 'lock'));

                if ((int)$this->_lastChunk['total'] === count($chunks) + 1) {
                    $this->_isLast = true;
                }

                if (!empty($this->_lastChunk['blob'])) {
                    if (empty($_FILES['content']['tmp_name'])) {
                        return false;
                    }

                    move_uploaded_file(
                        $_FILES['content']['tmp_name'],
                        $this->_chunkFolder . '/' . (int) $info['current']
                    );
                } else {
                    file_put_contents($this->_chunkFolder . '/' . (int) $info['current'], $info['content']);
                }

                flock($f, LOCK_UN);

                return true;
            } else {
                throw new Billiondigital_Core_PermissionException('Couldn\'t lock the file');
            }
        }
    }

    public function last() {
        return $this->_isLast;
    }

    public function complete() {
        $content = '';
        for ($i = 1, $count = (int) $this->_lastChunk['total']; $i <= $count; $i++) {
            if (!file_exists($this->_chunkFolder . "/$i"))
                throw new Mage_Adminhtml_Exception('Missing chunk #' . $i . ' : ' . implode(' / ', scandir($this->_chunkFolder)));

            $data = file_get_contents($this->_chunkFolder . "/$i");

            if (!empty($this->_lastChunk['encode']) || !empty($this->_lastChunk['zip'])) {
                $data = base64_decode($data);
            }

            $content .= $data;
        }
        Mage::helper('billioncore/filesystem')->remove($this->_chunkFolder, true);

        if (!empty($this->_lastChunk['zip'])) {
            $result = Mage::helper('billionthemler/zip')->unpackString($content, 'data');
        } else if (!empty($this->_lastChunk['encode'])) {
            $result = array(
                'status' => 'done',
                'data' => rawurldecode($content)
            );
        } else {
            $result = array(
                'status' => 'done',
                'data' => $content
            );
        }

        return $result;
    }

    private function validate($info) {
        if (empty($info['id']))
            throw new Mage_Adminhtml_Exception('Invalid id');
        if (!isset($info['total']) || (int) $info['total'] < 1)
            throw new Mage_Adminhtml_Exception('Invalid chunks total');
        if (!isset($info['current']) || (int) $info['current'] < 1)
            throw new Mage_Adminhtml_Exception('Invalid current chunk number');
        if (empty($_FILES['content']) && empty($info['content']))
            throw new Mage_Adminhtml_Exception('Invalid chunk content');
    }

}

//