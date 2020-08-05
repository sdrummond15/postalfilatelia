<?php
//

class Billiondigital_Core_Helper_Filesystem extends Mage_Core_Helper_Abstract
{
    public function mkdir($dir, $mode = 0777, $recursive = true) {
        if (!file_exists($dir)) {
            if (!mkdir($dir, $mode, $recursive)) {
                throw new Billiondigital_Core_PermissionException('Write permission denied: ' . $dir);
            }
        }
    }

    public function remove($dir, $deleteRootToo = false)
    {
        if (!is_dir($dir)) {
            if (is_writable($dir)) {
                unlink($dir);
            }
            return false;
        }

        if (!file_exists($dir)) {
            return false;
        }

        $list = $this->enumerateDir($dir, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($list as $item) {
            if (!is_writable($item->realPath)) continue;
            if ($item->isDir) {
                rmdir($item->realPath);
            } else {
                unlink($item->realPath);
            }
        }

        if ($deleteRootToo && is_writable($dir)) {
            return rmdir($dir);
        }

        return true;
    }

    public function copy($src, $dst)
    {
        if (is_file($src)) {
            $dir = dirname($dst);
            $this->mkdir($dir, 0777, true);

            if (is_writable($dir)) {
                copy($src, $dst);
            } else {
                throw new Billiondigital_Core_PermissionException('Write permission denied: ' . $dst);
            }
        } else if (is_dir($src)) {
            $this->mkdir($dst);

            $list = $this->enumerateDir($src);

            foreach ($list as $item) {
                if ($item->isDir) {
                    $this->mkdir($dst . DS . $item->subPathName);
                } else {
                    if (!copy($item->realPath, $dst . DS . $item->subPathName)) {
                        throw new Billiondigital_Core_PermissionException(
                            'Permission denied: copy ' . $item->realPath . ' to ' . $dst . DS . $item->subPathName
                        );
                    }
                }
            }
        } else {
            throw new Mage_Core_Exception('Fso copy: Wrong arguments');
        }
    }

    public function rename($old, $new, $safe = true)
    {
        if (!file_exists($old)) {
            throw new Billiondigital_Core_PermissionException('Fso rename: could not find source');
        } else if ($safe && file_exists($new)) {
            throw new Billiondigital_Core_PermissionException('Fso rename: target already exists');
        } else {
            $result = rename($old, $new);
            if (!$result) {
                throw new Billiondigital_Core_PermissionException('Fso rename: permission denied');
            }
        }
    }

    public function enumerateDir($dir, $option = RecursiveIteratorIterator::SELF_FIRST) {
        $list = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
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

    /**
     * @param $path string
     * @return array File list
     */
    public function enumerate($path, $deep = true, $re = null, $dirs = false)
    {
        $files = array();
        if (is_dir($path)) {
            if ($handle = opendir($path)) {
                while (($name = readdir($handle)) !== false) {
                    if (!preg_match('#^\.#', $name)) {
                        if (is_dir($path . '/' . $name)) {
                            if ($dirs) $files[] = $path . '/' . $name;
                            if (!$deep) continue;
                            $files = array_merge($files, $this->enumerate($path . '/' . $name, $deep, $re, $dirs));
                        } else {
                            if ($re && !preg_match($re, $name)) continue;
                            $files[] = $path . '/' . $name;
                        }
                    }
                }

                closedir($handle);
            }
        }

        return $files;
    }

    /**
     * @param $path
     * @param $contents
     */
    public function write($path, $contents, $flags = 0)
    {
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!is_writable($dir)) {
            throw new Billiondigital_Core_PermissionException('Write permission denied: ' . $dir);
        } else if (file_exists($path) && !is_writable($path)) {
            throw new Billiondigital_Core_PermissionException('Write permission denied: ' . $path);
        } else {
            file_put_contents($path, $contents, $flags);
        }
    }

    public function read($path)
    {
        $content = null;
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if ($content === false)
                throw new Billiondigital_Core_PermissionException('Read permission denied: ' . $path);
        }
        return $content;
    }

    public function checkDirPermissions($path)
    {
        return is_dir($path) && is_writable($path) && is_readable($path);
    }

    public function checkFilePermissions($path)
    {
        return file_exists($path) && is_writable($path) && is_readable($path);
    }
}

//