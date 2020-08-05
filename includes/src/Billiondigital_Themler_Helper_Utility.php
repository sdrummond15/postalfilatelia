<?php
//

class Billiondigital_Themler_Helper_Utility extends Mage_Core_Helper_Abstract
{
    public function commonErrorHandler() {
        if ($e = error_get_last()) {
            switch ($e['type']) {
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_PARSE:
                    printf('[PHP_ERROR]%s[PHP_ERROR]', json_encode($e));
            }
        }
    }

    public function registerCommonErrorHandler() {
        @error_reporting(E_ERROR | E_PARSE | error_reporting());
        @ini_set('display_errors', 1);
        register_shutdown_function(array($this, 'commonErrorHandler'));
    }

    /**
     * @return float|int
     */
    public function getMaxRequestSize()
    {
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));
        $memorySize = $this->toBytes(ini_get('memory_limit'));

        return min($postSize, $uploadSize, $memorySize);
    }

    /**
     * @param $str
     *
     * @return int
     */
    public function toBytes($str) {
        $str = strtolower(trim($str));

        if ($str) {
            switch ($str[strlen($str) - 1]) {
                case 'g':
                    $str *= 1024;
                case 'm':
                    $str *= 1024;
                case 'k':
                    $str *= 1024;
            }
        }

        return intval($str);
    }

    public function getMemoryLimit()
    {
        if (!function_exists('ini_get'))
            return -1;
        return $this->toBytes(ini_get('memory_limit'));
    }

    /**
     * @param $testMemoryAlloc
     */
    public function checkMemoryLimit($testMemoryAlloc)
    {
        $requiredSize = 64 * 1024 * 1024;
        $memory = $this->getMemoryLimit();

        // can't retrieve memory limit option
        if (-1 == $memory)
            return;

        // try to increase limit
        if ($memory < $requiredSize) {
            if(!function_exists('ini_set'))
                $this->_outOfMemoryHandler(false);

            $ret = ini_set('memory_limit', '64M');
            if (!$ret)
                $this->_outOfMemoryHandler(false);
        }

        // check real limits
        if ($testMemoryAlloc) {
            $this->_testMemoryAlloc();
        }
    }

    //http://stackoverflow.com/questions/2726524/can-you-unregister-a-shutdown-function
    public function memoryLimitShutdown() {
        $error = error_get_last();
        if ($error && $error['type'] === E_ERROR && !isset($GLOBALS['memory_test_passed'])) {
            $this->_outOfMemoryHandler(true);
        }
    }

    private function _testMemoryAlloc() {
        // try to allocate 16Mb
        $allocBytes = 16 * 1024 * 1024;
        register_shutdown_function(array($this, 'memoryLimitShutdown'));

        $tmp = @str_repeat('.', $allocBytes);
        unset($tmp);
        $GLOBALS['memory_test_passed'] = true;

        return true;
    }

    function _outOfMemoryHandler($alloc) {
        $head = <<<EOL
                <head>
                <style>
                html{background:#eee}.body{background:#fff;color:#333;font-family:"Open Sans",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:0 1px 3px rgba(0,0,0,.13);box-shadow:0 1px 3px rgba(0,0,0,.13)}h3{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px "Open Sans",sans-serif;margin:30px 0 0;padding:0 0 7px}#error-page{margin-top:50px}#error-page p{font-size:14px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:14px}a{color:#21759B;text-decoration:none}a:hover{color:#D54E21}
                </style>
                </head>
EOL;

        if ($alloc) {
            die(<<<EOL
                <html>
                $head
                <body>
                <div class="body">
                <h3>PHP Memory Configuration Error</h3>
                <p>Themler requires at least 64Mb of PHP memory. Please increase your PHP memory to continue.
                For more information, please check this <a href="http://answers.billiondigital.com/articles/5826/out-of-memory" target="_blank">link</a>.</p>
                </div>
                </body>
                </html>
EOL
            );
        } else {
            $memoryLimit = $this->getMemoryLimit() / 1024 / 1024 . 'Mb';
            die(<<<EOL
                <html>
                $head
                <body>
                <div class="body">
                <h3>PHP Memory Configuration Error</h3>
                <p>Themler requires at least 64Mb of PHP memory (you have "$memoryLimit"). Please increase your PHP memory to continue.
                For more information, please check this <a href="http://answers.billiondigital.com/articles/5826/out-of-memory" target="_blank">link</a>.</p>
                </div>
                </body>
                </html>
EOL
            );
        }
    }
}

//