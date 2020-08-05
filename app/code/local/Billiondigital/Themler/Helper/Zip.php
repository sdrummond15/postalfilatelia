<?php
//

class Billiondigital_Themler_Helper_Zip
{
    public function pack($source, $wrapDir = '', $exclude = array()) {
        if ($wrapDir && substr($wrapDir, -1) !== '/')
            $wrapDir .= '/';

        $outPath = tempnam('tmp', 'zip');
        $z = new ZipArchive();
        $res = $z->open($outPath, ZipArchive::CREATE);

        if ($res === true) {
            $list = $this->enumerateDir($source, function ($current) use ($exclude) {
                return !in_array($current->getFilename(), $exclude);
            });

            foreach ($list as $item) {
                if (!$item->isDir) {
                    $z->addFile($item->realPath, $wrapDir . $item->subPathName);
                }
            }

            $z->close();
        } else {
            return array('status' => 'error', 'message' => self::message($res));
        }

        return array('status' => 'done', 'path' => $outPath);
    }

    public function unpack($file, $target) {
        $zip = new ZipArchive;
        $res = $zip->open($file);
        Mage::helper('billioncore/filesystem')->mkdir($target, 0777, true);

        if ($res === true) {
            for($i = 0; $i < $zip->numFiles; $i++) {
                $data = $zip->getFromIndex($i);
                $filename = preg_replace('#[/\\\]#', DS, $zip->getNameIndex($i));
                $dest = $target . DS . $filename;

                if (substr($dest, -1) !== DS) {
                    Mage::helper('billioncore/filesystem')->mkdir(dirname($dest), 0777, true);
                    file_put_contents($dest, $data);
                }
            }

            $zip->close();
            return array('status' => 'done');
        } else {
            return array('status' => 'error', 'message' => self::message($res));
        }
    }

    public function unpackString($str, $key) {
        $zipPath = tempnam('tmp', 'str_zip');
        file_put_contents($zipPath, $str);

        $unzipPath = MAGENTO_ROOT . DS . 'var/unzip_string';
        Mage::helper('billioncore/filesystem')->mkdir($unzipPath, 0777, true);

        $result = $this->unpack($zipPath, $unzipPath);
        if ($result['status'] === 'done' && file_exists($unzipPath . DS . $key)) {
            $result['data'] = file_get_contents($unzipPath . DS . $key);
        } else {
            $result['message'] = 'unzip error';
        }

        Mage::helper('billioncore/filesystem')->remove($unzipPath, true);

        return $result;
    }

    public static function message($code)
    {
        switch ($code)
        {
            case 0:
                return 'No error';

            case 1:
                return 'Multi-disk zip archives not supported';

            case 2:
                return 'Renaming temporary file failed';

            case 3:
                return 'Closing zip archive failed';

            case 4:
                return 'Seek error';

            case 5:
                return 'Read error';

            case 6:
                return 'Write error';

            case 7:
                return 'CRC error';

            case 8:
                return 'Containing zip archive was closed';

            case 9:
                return 'No such file';

            case 10:
                return 'File already exists';

            case 11:
                return 'Can\'t open file';

            case 12:
                return 'Failure to create temporary file';

            case 13:
                return 'Zlib error';

            case 14:
                return 'Malloc failure';

            case 15:
                return 'Entry has been changed';

            case 16:
                return 'Compression method not supported';

            case 17:
                return 'Premature EOF';

            case 18:
                return 'Invalid argument';

            case 19:
                return 'Not a zip archive';

            case 20:
                return 'Internal error';

            case 21:
                return 'Zip archive inconsistent';

            case 22:
                return 'Can\'t remove file';

            case 23:
                return 'Entry has been deleted';

            default:
                return 'An unknown error has occurred(' . intval($code) . ')';
        }
    }

    function enumerateDir($dir, $filter = null, $option = RecursiveIteratorIterator::SELF_FIRST) {
        $list = array();

        if (!$filter) {
            $filter = function () {
                return true;
            };
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
                $filter
            ),
            $option
        );

        foreach ($iterator as $item) {
            $f = new stdClass;
            $f->isDir = $item->isDir();
            $f->subPathName = $iterator->getSubPathName();
            $f->realPath = $item->getRealPath();
            $f->fileName = $item->getFilename();
            $list[] = $f;
        }

        $iterator = null;

        return $list;
    }
}

//