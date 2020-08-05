<?php
// 
    header('Content-Type: text/css');
    header('Expires: on, 01 Jan 1970 00:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');

    $css = '';

    if ($css = file_get_contents('style.css')) {
        $replacer = new CssReplacer();
        $css = preg_replace_callback('#url\([\'"]?(images[\\\/]designer[\\\/][^\\\/\)\'"]+)[\'"]?\)#',
            array($replacer, 'updatePath'),
            $css);

        echo $css;
    }

    class CssReplacer {
        private $_template;

        function __construct()
        {
            $this->_template = empty($_GET['template']) ? '' : $_GET['template'];
        }

        public function updatePath($matches)
        {
            if ($this->_template) {
                $basePath = '../' . $this->_template . '/' . $matches[1];
                return file_exists($basePath) ? str_replace($matches[1], $basePath, $matches[0]) : $matches[0];
            }
        }
    }
//