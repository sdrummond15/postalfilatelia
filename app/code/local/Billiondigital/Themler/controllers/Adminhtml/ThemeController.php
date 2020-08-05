<?php
//

class Billiondigital_Themler_Adminhtml_ThemeController extends Billiondigital_Themler_Controller_AdminAction
{
    protected static $APP_DIR;
    protected static $SKIN_DIR;

    protected static $PREVIEW_DIRNAME;
    protected static $THEME_DIRNAME;

    /**
     * @var Billiondigital_Core_Model_Config
     */
    protected $cfg;
    /**
     * @var Billiondigital_Core_Helper_Filesystem
     */
    protected $fso;
    /**
     * @var Billiondigital_Themler_Model_Export_Preview
     */
    protected $preview;
    /**
     * @var Billiondigital_Core_Helper_Path
     */
    protected $path;

    public $_publicActions = array('index', 'project', 'getManifest');

    /**
     * Overrides base constructor
     */
    protected function _construct() {
        parent::_construct();

        $this->cfg = Mage::getSingleton('billioncore/config');
        $this->fso = Mage::helper('billioncore/filesystem');
        $this->path = Mage::helper('billioncore/path');
        $this->preview = Mage::getSingleton('billionthemler/export_preview', array('theme' => $this->currentTheme));

        self::$APP_DIR = MAGENTO_ROOT . $this->cfg->get('paths/app_base');
        self::$SKIN_DIR = MAGENTO_ROOT . $this->cfg->get('paths/skin_base');

        self::$PREVIEW_DIRNAME = $this->cfg->get('paths/preview/dirname', array('theme' => $this->currentTheme));
        self::$THEME_DIRNAME = $this->cfg->get('paths/theme/dirname', array('theme' => $this->currentTheme));
    }

    /**
     * Loads Billion Themler Admin Page
     */
    public function indexAction()
    {
        if ($this->getRequest()->isAjax()) {
            $action = $this->getRequest()->getParam('action', '');
            if (method_exists($this, $action . 'Action')) {
                $this->{$action . 'Action'}();
            } else {
                $this->setAjaxResponse(array('result' => 'fail', 'error' => 'Wrong request'));
            }
        } else {
            Mage::helper('billionthemler/utility')->checkMemoryLimit(true);
            $this->loadLayout();
            $root = $this->getLayout()->getBlock('root');
            $root->setTheme($this->currentTheme);
            if (!$this->currentTheme || !Billiondigital_Themler_Model_Export_Project::exists($this->currentTheme)) {
                $root->setTemplate('billioncore/error.phtml')
                    ->setErrorTemplate('billioncore/noroute.phtml');
            } else if (!$this->_checkPermissions()) {
                $root->setDirs(Billiondigital_Core_PermissionException::$themeFolders)
                    ->setTemplate('billioncore/error.phtml')
                    ->setErrorTemplate('billioncore/permission-denied.phtml');
            }
            $this->renderLayout();
        }
    }

    public function projectAction()
    {
        $this->loadLayout();
        $root = $this->getLayout()->getBlock('root');
        $root->setTheme($this->currentTheme);
        $this->getResponse()->setHeader('Content-type', 'application/javascript');
        $this->renderLayout();
    }

    /**
     *  Starts Export
     */
    public function exportAction()
    {
        $this->setAjaxResponse($this->_processChunk('_exportCallback'));
    }

    private function _exportCallback($content) {
        Varien_Profiler::start('themler::export::_buildExport');
        $this->_buildExport($content);
        Varien_Profiler::stop('themler::export::_buildExport');

        if (array_key_exists('projectData', $content)) {
            Varien_Profiler::start('themler::export::Save project');
            $project = Mage::getSingleton('billionthemler/export_project', array($this->currentTheme, $content));
            $project->save();
            Varien_Profiler::stop('themler::export::Save project');

            Varien_Profiler::start('themler::export::Save diff');
            /** @var Billiondigital_Themler_Model_Export_Storage_Abstract $diff */
            $diff = Mage::getSingleton('billionthemler/export_storage_diff', array('theme' => $this->currentTheme));
            $this->_updateTheme($diff->load()->toArray(), true);
            $diff->remove();
            Varien_Profiler::stop('themler::export::Save diff');

            Varien_Profiler::start('themler::export::Update images');
            $this->_updateImages($content['images']);
            $this->fso->remove($this->path->previewSkinDir($this->currentTheme) . '/images/designer/');
            Varien_Profiler::stop('themler::export::Update images');
            $this->fso->remove($this->path->themeSkinDir($this->currentTheme) . '/bootstrap.min.css');
            $this->fso->remove($this->path->themeSkinDir($this->currentTheme) . '/style.min.css');

            Varien_Profiler::start('themler::export::Update modules');
            $this->_updateModules();
            Varien_Profiler::stop('themler::export::Update modules');
        }

        if (array_key_exists('cssJsSources', $content)) {
            Varien_Profiler::start('themler::export::Update CSS/JS Sources');
            $this->_updateCache($content['cssJsSources']);
            Varien_Profiler::stop('themler::export::Update CSS/JS Sources');
        }

        if (array_key_exists('md5Hashes', $content)) {
            Varien_Profiler::start('themler::export::Update MD5 hashes');
            $this->_updateHashes($content['md5Hashes']);
            Varien_Profiler::stop('themler::export::Update MD5 hashes');
        }

        return array('result' => 'done');
    }

    /**
     * Makes theme active
     */
    public function publishAction() {
        $themeName = $this->stripThemeName($this->getRequest()->getParam('themeName'));
        if (!$themeName) {
            $themeName = $this->currentTheme;
        }

        if (!is_dir($this->path->themeAppDir($themeName))) {
            $result = array('result' => 'fail', 'error' => 'Wrong theme name');
        } else {
            Mage::getConfig()->saveConfig('design/theme/default', $themeName);
            Mage::getConfig()->reinit();
            Mage::app()->reinitStores();
            $result = array('result' => 'OK');
        }

        if ($this->getRequest()->isAjax()) {
            $this->setAjaxResponse($result);
        } else {
            $this->adminRedirect('billiontheme/adminhtml_list/index');
        }
    }

    /**
     * Clears chunks
     */
    public function clearAction() {
        $uploadPath = $this->cfg->get('paths/export/upload');
        try {
            if (($id = $this->getRequest()->getParam('id')) && $id && is_dir($uploadPath . DS . $id)) {
                Mage::helper('billioncore/filesystem')->remove($uploadPath . DS . $id, true);
                $this->setAjaxResponse(array('result' => 'OK'));
            } else {
                $this->setAjaxResponse(array('result' => 'fail'));
            }
        } catch (Billiondigital_Core_PermissionException $e) {
            $this->setAjaxResponse(array('result' => 'fail', 'type' => 'permission', 'error' => $e->getHtmlMessage()));
        }
    }

    /**
     * Update preview theme
     */
    public function updatePreviewAction()
    {
        $previewThemeAppDir = self::$APP_DIR . self::$PREVIEW_DIRNAME;
        $previewThemeSkinDir = self::$SKIN_DIR . self::$PREVIEW_DIRNAME;

        $themeAppDir = self::$APP_DIR . self::$THEME_DIRNAME;
        $themeSkinDir = self::$SKIN_DIR . self::$THEME_DIRNAME;

        try {
            // checks preview theme
            if (!is_dir($previewThemeAppDir)) {
                $this->_createPreview($themeAppDir, $previewThemeAppDir);
                $this->fso->remove($previewThemeAppDir . '/designer', true);
            }
            if (!is_dir($previewThemeSkinDir)) {
                $this->_createPreview($themeSkinDir, $previewThemeSkinDir);
            }

            /** @var Billiondigital_Themler_Model_Export_Storage_Abstract $diff */
            $diff = Mage::getSingleton('billionthemler/export_storage_diff', array('theme' => $this->currentTheme));
            $this->_updateTheme($diff->load()->toArray(), false); // restores preview theme state to base
            $diff->remove();

            $this->setAjaxResponse();
        } catch (Billiondigital_Core_PermissionException $e) {
            $this->setAjaxResponse(array('result' => 'fail', 'type' => 'permission', 'error' => $e->getHtmlMessage()));
        }
    }

    public function getThemeAction() {
        try {
            $themeName = $this->getRequest()->getParam('themeName', $this->currentTheme);
            $newName = $this->getRequest()->getParam('newName', $this->currentTheme);
            $includeEditor = (bool) json_decode($this->getRequest()->getParam('includeEditor')); // true/false strings

            $packageData = Mage::helper('billionthemler/package')
                                ->createPackage($themeName, $newName, $includeEditor);

            $this->getResponse()->clearBody();
            $this->getResponse()->clearHeaders()
                ->setHeader('Content-Type', 'application/x-gzip')
                ->setHeader('Content-Disposition', 'inline; filename="' . $newName . '.tgz"');
            $this->getResponse()->sendHeaders();
            $this->getResponse()->setBody($packageData);
        } catch (Billiondigital_Core_PermissionException $e) {
            $this->setAjaxResponse(array('result' => 'fail', 'type' => 'permission', 'error' => $e->getHtmlMessage()));
        }
    }

    public function canRenameAction() {
        $newThemeName = $this->stripThemeName($this->getRequest()->getPost('newName'));

        if (trim($newThemeName)) {
            $newPreview = $this->cfg->get('paths/preview/dirname', array('theme' => $newThemeName));
            $newBase = $this->cfg->get('paths/theme/dirname', array('theme' => $newThemeName));

            $this->setAjaxResponse(array(
                'result' => 'OK',
                'canRename' => !is_dir(self::$APP_DIR . $newPreview) && !is_dir(self::$APP_DIR . $newBase) &&
                               !is_dir(self::$SKIN_DIR . $newPreview) && !is_dir(self::$SKIN_DIR . $newBase)
            ));
        } else {
            $this->setAjaxResponse(array('result' => 'fail', 'error' => 'invalid name param'));
        }
    }

    public function copyAction()
    {
        $themeName = $this->stripThemeName($this->getRequest()->getPost('themeName'));
        $newThemeName = $this->stripThemeName($this->getRequest()->getPost('newName'));

        try {
            // theme app
            $themeApp = $this->path->themeAppDir($themeName);
            $previewApp = $this->path->previewAppDir($themeName);
            // theme skin
            $themeSkin = $this->path->themeSkinDir($themeName);
            $previewSkin = $this->path->previewSkinDir($themeName);

            if (!is_dir($themeApp) || !is_dir($previewApp) ||
                !is_dir($themeSkin) || !is_dir($previewSkin)) {

                throw new Mage_Adminhtml_Exception('Wrong source theme');
            }

            if (!$newThemeName) {
                $newThemeName = $themeName;
            }

            $newThemeName = Mage::helper('billionthemler/package')->getAvailableThemeName($newThemeName);

            // new app
            $newThemeApp = $this->path->themeAppDir($newThemeName);
            $newPreviewApp = $this->path->previewAppDir($newThemeName);
            // new skin
            $newThemeSkin = $this->path->themeSkinDir($newThemeName);
            $newPreviewSkin = $this->path->previewSkinDir($newThemeName);

            $this->fso->copy($previewApp, $newPreviewApp);
            $this->fso->copy($themeApp, $newThemeApp);

            $this->fso->copy($previewSkin, $newPreviewSkin);
            $this->fso->copy($themeSkin, $newThemeSkin);

            $result = array('result' => 'OK');
        } catch (Billiondigital_Core_PermissionException $e) {
            $result = array('result' => 'fail', 'type' => 'permission', 'error' => $e->getHtmlMessage());
        } catch (Exception $e) {
            $result = array('result' => 'fail', 'error' => $e->getMessage());
        }

        $this->setAjaxResponse($result);
    }

    public function renameAction()
    {
        $themeName = $this->stripThemeName($this->getRequest()->getPost('themeName'));
        $newThemeName = $this->stripThemeName($this->getRequest()->getPost('newName'));

        try {
            // theme app
            $themeApp = $this->path->themeAppDir($themeName);
            $previewApp = $this->path->previewAppDir($themeName);
            // theme skin
            $themeSkin = $this->path->themeSkinDir($themeName);
            $previewSkin = $this->path->previewSkinDir($themeName);

            if (!is_dir($themeApp) || !is_dir($previewApp) ||
                    !is_dir($themeSkin) || !is_dir($previewSkin)) {

                throw new Mage_Adminhtml_Exception('Wrong source theme');
            } else if (!is_null($newThemeName)) {

                $cfg = Mage::getResourceModel('core/config_data_collection')
                    ->addFieldToSelect('*')
                    ->addPathFilter('designer/settings/' . $themeName)
                    ->load();

                if (!trim($newThemeName)) {
                    if ($themeName === $this->currentTheme || Mage::helper('billioncore')->isActive($themeName)) {
                        throw new Mage_Adminhtml_Exception('Remove active theme');
                    }

                    $this->fso->remove($themeApp, true);
                    $this->fso->remove($previewApp, true);
                    $this->fso->remove($themeSkin, true);
                    $this->fso->remove($previewSkin, true);

                    foreach ($cfg as $row) {
                        Mage::getModel('core/config_data')->setId($row->getId())->delete();
                    }
                } else {
                    // new app
                    $newThemeApp = $this->path->themeAppDir($newThemeName);
                    $newPreviewApp = $this->path->previewAppDir($newThemeName);
                    // new skin
                    $newThemeSkin = $this->path->themeSkinDir($newThemeName);
                    $newPreviewSkin = $this->path->previewSkinDir($newThemeName);

                    if (is_dir($newThemeApp) || is_dir($newPreviewApp)
                        || is_dir($newThemeSkin) || is_dir($newPreviewSkin)) {

                        throw new Mage_Adminhtml_Exception('Theme already exists');
                    }

                    $this->fso->rename($previewApp, $newPreviewApp);
                    $this->fso->rename($themeApp, $newThemeApp);

                    $this->fso->rename($previewSkin, $newPreviewSkin);
                    $this->fso->rename($themeSkin, $newThemeSkin);

                    foreach ($cfg as $row) {
                        $id = $row->getId();
                        $path = $row->getPath();
                        $model = Mage::getModel('core/config_data')->load($id)->setPath(
                            str_replace(
                                'designer/settings/' . $themeName,
                                'designer/settings/' . $newThemeName,
                                $path
                            )
                        );
                        $model->setId($id)->save();
                    }

                    if (Mage::helper('billioncore')->isActive($themeName)) {
                        Mage::getConfig()->saveConfig('design/theme/default', $newThemeName);
                    }
                }

                $result = array('result' => 'OK');
            } else {
                throw new Mage_Adminhtml_Exception('Invalid name param');
            }
        } catch (Billiondigital_Core_PermissionException $e) {
            $result = array('result' => 'fail', 'type' => 'permission', 'error' => $e->getHtmlMessage());
        } catch (Exception $e) {
            $result = array('result' => 'fail', 'error' => $e->getMessage());
        }

        $this->setAjaxResponse($result);
    }

    public function reloadThemesInfoAction()
    {
        $this->setAjaxResponse(array(
            'info' => array('themes' => Mage::helper('billionthemler')->getThemesInfo()))
        );
    }

    public function getManifestAction()
    {
        try {
            $version = $this->getRequest()->getParam('version');
            $this->getResponse()->clearBody();
            $this->getResponse()->clearHeaders()
                 ->setHeader('Content-Type', 'text/cache-manifest')
                 ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                 ->setHeader('Cache-Control', 'post-check=0, pre-check=0', false)
                 ->setHeader('Pragma', 'no-cache');

            $content = Billiondigital_Themler_Model_Manifest::open($version)->getContent();
            $this->getResponse()->setBody(
                Mage::app()->getStore()->isCurrentlySecure() ? str_replace('http://', 'https://', $content) : $content
            );
        } catch (Billiondigital_Core_PermissionException $e) {
            $this->setAjaxResponse(array('result' => 'fail', 'type' => 'permission', 'error' => $e->getHtmlMessage()));
        }
    }

    public function getFilesAction()
    {
        $mask = $this->getRequest()->getPost('mask', '*');
        $filter = $this->getRequest()->getPost('filter', '');
        $files = array();

        try {
            $app = $this->_getFiles(self::$APP_DIR . self::$THEME_DIRNAME . '/{' . $mask . '}', GLOB_BRACE);
            $skin = $this->_getFiles(self::$SKIN_DIR . self::$THEME_DIRNAME . '/{' . $mask . '}', GLOB_BRACE);

            foreach (array_merge($app, $skin) as $file) {
                $filename = preg_replace('#[\\/]+#', '/', $file);
                $filename = str_replace(MAGENTO_ROOT, '', $filename);

                if (is_dir($file) || $filter && preg_match("#$filter#", $filename)) {
                    continue;
                }

                if (!is_readable($file)) {
                    throw new Billiondigital_Core_PermissionException('Read permission denied: ' . $file);
                }

                $files[$filename] = file_get_contents($file);
            }

            $this->setAjaxResponse(array('result' => 'OK', 'files' => $files));
        } catch (Billiondigital_Core_PermissionException $e) {
            $this->setAjaxResponse(array('result' => 'fail', 'type' => 'permission', 'error' => $e->getHtmlMessage()));
        }
    }

    public function setFilesAction()
    {
        $this->setAjaxResponse($this->_processChunk('_setFilesCallback'));
    }

    private function _setFilesCallback($files) {
        if ($files && count($files)) {
            foreach ($files as $filename => $content) {
                if ('themler.manifest' === $filename && preg_match('/#ver:(\d+)/i', $content, $matches)) {
                    Mage::getModel('billionthemler/manifest', array($matches[1], $content))
                        ->updateThemeVersion($this->currentTheme)
                        ->save();

                    $filename = Mage::helper('billioncore/path')->themeManifest($this->currentTheme, $matches[1]);
                } else {
                    $filename = MAGENTO_ROOT . $filename;
                }
                $this->fso->write($filename, $content, LOCK_EX);
            }
        }
        return array('result' => 'done');
    }

    public function uploadImageAction()
    {
        $filename = $this->getRequest()->getParam('filename', '');
        $skinSubFolder = 'images/designer/';

        if (!$filename) {
            $result = array(
                'status' => 'error',
                'message' => 'Empty file name'
            );
        } else {
            $uploadPath = self::$SKIN_DIR . self::$PREVIEW_DIRNAME . DS . $skinSubFolder . $filename;

            try {
                $result = $this->_uploadFileChunk($uploadPath);
                if ($result['status'] === 'done') {
                    $result['url'] = Mage::getDesign()->getSkinUrl(
                        $skinSubFolder . $filename,
                        array(
                            '_area' => 'frontend',
                            '_package' => 'default',
                            '_theme' => preg_replace('/[\/\\\]/', '', self::$PREVIEW_DIRNAME)
                        )
                    );

                    $diff = Mage::getModel('billionthemler/export_storage_diff', array('theme' => $this->currentTheme));
                    $diff->load();
                    $diff->update(array(
                        preg_replace('/[\/\\\]/', DS, $uploadPath)
                    ));
                    $diff->save();
                }
            } catch (Billiondigital_Core_PermissionException $e) {
                $result = array(
                    'status' => 'error',
                    'message' => $e->getHtmlMessage(),
                    // DataProvider compatibility
                    'result' => 'fail',
                    'type' => 'permission',
                    'error' => $e->getHtmlMessage()
                );
            }
        }

        $this->setAjaxResponse($result);
    }

    public function uploadThemeAction()
    {
        $filename = $this->getRequest()->getParam('filename', '');

        if (!$filename) {
            $result = array(
                'status' => 'error',
                'message' => 'Empty file name'
            );
        } else {
            $uploadPath = MAGENTO_ROOT . $this->cfg->get('paths/export/upload') . DS . $filename;

            try {
                $result = $this->_uploadFileChunk($uploadPath);

                if ($result['status'] === 'done') {
                    $error = Mage::helper('billionthemler/package')->unpackPackage($uploadPath);
                    if ($error) {
                        $result = array('status' => 'error', 'message' => $error);
                    }
                }
            } catch (Billiondigital_Core_PermissionException $e) {
                $result = array(
                    'status' => 'error',
                    'message' => $e->getHtmlMessage(),
                    // DataProvider compatibility
                    'result' => 'fail',
                    'type' => 'permission',
                    'error' => $e->getHtmlMessage()
                );
            }
        }

        $this->setAjaxResponse($result);
    }

    public function fsoToZipAction()
    {
        $this->setAjaxResponse($this->_processChunk('_fsoToZipCallback'));
    }

    private function _fsoToZipCallback($content) {
        Varien_Profiler::start('themler::zipFso::compress');

        $fsoPath = MAGENTO_ROOT . $this->cfg->get('paths/export/fso');
        $this->fso->remove($fsoPath, true);
        Mage::helper('billionthemler/export')->unpackFso($content['fso'], $fsoPath);
        $result = Mage::helper('billionthemler/zip')->pack($fsoPath);

        Varien_Profiler::stop('themler::zipFso::compress');

        if ($result['status'] === 'done') {
            Varien_Profiler::start('themler::zipFso::encode');
            $status = array(
                'result' => 'done',
                'data' => base64_encode(file_get_contents($result['path']))
            );
            $this->fso->remove($result['path']);
            Varien_Profiler::stop('themler::zipFso::encode');
        } else {
            $status = array('result' => 'fail', 'message' => $result['message']);
        }

        return $status;
    }

    public function zipToFsoAction() {
        $filename = $this->getRequest()->getParam('filename', '');

        if (!$filename) {
            $result = array(
                'status' => 'error',
                'message' => 'Empty file name'
            );
        } else {
            $uploadPath = MAGENTO_ROOT . $this->cfg->get('paths/export/upload') . DS . $filename;

            try {
                $result = $this->_uploadFileChunk($uploadPath);

                if ($result['status'] === 'done') {
                    $extractPath = $uploadPath . '_contents';
                    $result = Mage::helper('billionthemler/zip')->unpack($uploadPath, $extractPath);
                    if ($result['status'] === 'done') {
                        $result['fso'] = Mage::helper('billionthemler/export')->packFso($extractPath);
                    }
                    $this->fso->remove($extractPath, true);
                }
            } catch (Billiondigital_Core_PermissionException $e) {
                $result = array(
                    'status' => 'error',
                    'message' => $e->getHtmlMessage(),
                    // DataProvider compatibility
                    'result' => 'fail',
                    'type' => 'permission',
                    'error' => $e->getHtmlMessage()
                );
            }

            $this->fso->remove($uploadPath, true);
        }

        $this->setAjaxResponse($result);
    }

    private function _uploadFileChunk($uploadPath)
    {
        $result = array();

        $contentRange = $this->getRequest()->getHeader('Content-Range');
        $isLast = $this->getRequest()->getParam('last', '');


        if (!isset($_FILES['chunk']) || !file_exists($_FILES['chunk']['tmp_name'])) {
            $result = array(
                'status' => 'error',
                'message' => 'Empty chunk data'
            );
        } else if (!$contentRange && !$isLast) {
            $result = array(
                'status' => 'error',
                'message' => 'Empty Content-Range header'
            );
        } else {
            $rangeBegin = 0;

            if ($contentRange) {
                $contentRange = str_replace('bytes ', '', $contentRange);
                list($range, $total) = explode('/', $contentRange);
                list($rangeBegin, $rangeEnd) = explode('-', $range);
            }

            $tmpPath = MAGENTO_ROOT . $this->cfg->get('paths/export/images') . DS . basename($uploadPath);
            $this->fso->mkdir(dirname($tmpPath), 0776, true);

            $f = fopen($tmpPath, 'c');

            if (flock($f, LOCK_EX)) {
                fseek($f, (int) $rangeBegin);
                fwrite($f, file_get_contents($_FILES['chunk']['tmp_name']));

                flock($f, LOCK_UN);
                fclose($f);
            } else {
                throw new Billiondigital_Core_PermissionException('Permission denied: ' . $tmpPath);
            }

            if ($isLast) {
                if (file_exists($uploadPath) && is_writable($uploadPath)) {
                    unlink($uploadPath);
                }

                $this->fso->mkdir(dirname($uploadPath), 0776, true);
                if (!is_writable(dirname($uploadPath))) {
                    throw new Billiondigital_Core_PermissionException('Permission denied: ' . $uploadPath);
                }

                rename($tmpPath, $uploadPath);
                $this->fso->remove(dirname($tmpPath));

                $result = array(
                    'status' => 'done'
                );
            } else {
                $result['status'] = 'processed';
            }
        }

        return $result;
    }

    private function _getFiles($mask, $flags)
    {
        $files = glob($mask, $flags);
        if (!is_array($files)) {
            $files = array();
        }
        $folders = glob(dirname($mask) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
        if (!is_array($folders)) {
            $folders = array();
        }
        foreach ($folders as $dir) {
            $files = array_merge($files, $this->_getFiles($dir . '/' . basename($mask), $flags));
        }

        return $files;
    }

    private function _getChunkInfo() {
        return array(
            'id' => $this->getRequest()->getParam('id', ''),
            'content' => $this->getRequest()->getParam('content', ''),
            'current' => $this->getRequest()->getParam('current', ''),
            'total' => $this->getRequest()->getParam('total', ''),
            'encode' => $this->getRequest()->getParam('encode', false),
            'blob' => $this->getRequest()->getParam('blob', false),
            'zip' => $this->getRequest()->getParam('zip', false)
        );
    }

    private function _processChunk($success) {
        if ($data = $this->getRequest()->getParam('info', false)) {
            $info = json_decode($data, true);
        } else {
            $info = $this->_getChunkInfo();
        }

        try {
            Varien_Profiler::start('themler::export::chunk_save');

            $chunk = Mage::getModel('billionthemler/export_chunk');

            if (!$chunk->save($info)) {
                $this->getResponse()
                    ->setHttpResponseCode(400)
                    ->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                return array();
            }

            Varien_Profiler::stop('themler::export::chunk_save');

            if ($chunk->last()) {
                $chunkResult = $chunk->complete();
                if ($chunkResult['status'] === 'done') {
                    Varien_Profiler::start('themler::export::Decode json');
                    $content = json_decode($chunkResult['data'], true);
                    Varien_Profiler::stop('themler::export::Decode json');

                    Varien_Profiler::start('themler::export::' . $success);
                    $response = call_user_func(array($this, $success), $content);
                    Varien_Profiler::stop('themler::export::' . $success);
                } else {
                    $chunkResult['result'] = 'error';
                    $response = $chunkResult;
                }
            } else {
                $response = array('result' => 'processed');
            }
        } catch (Billiondigital_Core_PermissionException $e) {
            $response = array('result' => 'fail', 'type' => 'permission', 'error' => $e->getHtmlMessage());
        }

        return $response;
    }

    private function _createPreview($from, $to)
    {
        $lockFile = self::$APP_DIR . '/lock';
        $ff = fopen($lockFile, 'c');
        if (flock($ff, LOCK_EX)) {
            if (!is_dir($to)) {
                mkdir($to, 0777, true);
            }
            foreach ($this->fso->enumerate($from) as $fromFile) {
                $toFile = str_replace($from, $to, $fromFile);
                $fromFile = str_replace('\\', '/', $fromFile);
                $toFile = str_replace('\\', '/', $toFile);
                $this->_restorePreviewFile($toFile, $fromFile);
            }
            flock($ff, LOCK_UN);
        } else {
            throw new Billiondigital_Core_PermissionException('Lock error: ' . $lockFile);
        }
    }

    /**
     * @param Array $updateList
     * @param bool $saveAction Update base theme if true, otherwise update preview (copy from or copy to list)
     * @throws Billiondigital_Core_PermissionException
     */
    private function _updateTheme($updateList, $saveAction)
    {
        if (!is_array($updateList) || !count($updateList)) return;

        $lock = fopen(self::$APP_DIR . '/lock', 'c');
        if (flock($lock, LOCK_EX)) {

            foreach ($updateList as $previewFile) {
                $previewFile = str_replace('\\', '/', $previewFile);
                $themeFile = $this->_getThemeFileName($previewFile);

                if ($saveAction) {
                    // update base theme from preview
                    $this->_updateThemeFile($themeFile, $previewFile);
                } else {
                    // restore preview theme from base
                    $this->_restorePreviewFile($previewFile, $themeFile);
                }
            }

            $this->preview->save();

            flock($lock, LOCK_UN);
        } else {
            throw new Billiondigital_Core_PermissionException('Lock error: ' . self::$APP_DIR . '/lock');
        }
    }

    private function _updateThemeFile($themeFile, $sourceFile)
    {
        if (file_exists($sourceFile)) {
            $themeFileDir = dirname($themeFile);
            $this->fso->mkdir($themeFileDir, 0777, true);

            if (strpos($themeFile, $this->path->appDir()) !== false) {
                $content = $this->preview->removeDataId($sourceFile);
                file_put_contents($themeFile, $content);
            } else if (preg_match('#[\\\/]images[\\\/]designer$#', $themeFileDir)) {
                // keep images in base theme only
                rename($sourceFile, $themeFile);
            } else {
                copy($sourceFile, $themeFile);
            }

        } else if (file_exists($themeFile)) {
            unlink($themeFile);
            $this->preview->removeKey($themeFile);
        }

    }

    private function _restorePreviewFile($previewFile, $sourceFile)
    {
        if (file_exists($sourceFile)) {
            $this->fso->mkdir(dirname($previewFile), 0777, true);

            if (strpos($previewFile, $this->path->appDir()) !== false) {
                $content = $this->preview->restoreDataId($sourceFile);
                file_put_contents($previewFile, $content);
            } else {
                copy($sourceFile, $previewFile);
            }

        } else if (file_exists($previewFile)) {
            unlink($previewFile);
        }

    }

    private function _getThemeFileName($diffFile)
    {
        /** @var Billiondigital_Themler_Helper_Path $this->path */
        $previewAppDir = $this->path->previewAppDir($this->currentTheme);
        $previewSkinDir = $this->path->previewSkinDir($this->currentTheme);

        $themeAppDir = $this->path->themeAppDir($this->currentTheme);
        $themeSkinDir = $this->path->themeSkinDir($this->currentTheme);

        $themeFile = '';

        if (strpos($diffFile, $previewAppDir) !== false) {
            $themeFile = str_replace($previewAppDir, $themeAppDir, $diffFile);
        } else if (strpos($diffFile, $previewSkinDir) !== false) {
            $themeFile = str_replace($previewSkinDir, $themeSkinDir, $diffFile);
        }

        return $themeFile;
    }

    /**
     * Builds export from controls collection
     * @param $content
     * @param array $params
     */
    private function _buildExport($content, $params = array())
    {
        $exportParams = array(
            'name' => $this->currentTheme,
            'content' => $content,
            'dirname' => self::$PREVIEW_DIRNAME
        );

        $exportParams = array_merge($exportParams, $params);

        Varien_Profiler::start('themler::export::_buildExport::build');
        $export = Mage::getModel('billionthemler/export_builder', $exportParams);
        $export->build();
        Varien_Profiler::stop('themler::export::_buildExport::build');

        Varien_Profiler::start('themler::export::_buildExport::saveTo');
        $export->saveTo(MAGENTO_ROOT);
        Varien_Profiler::stop('themler::export::_buildExport::saveTo');

        Varien_Profiler::start('themler::export::_buildExport::clear');
        if (!$this->getRequest()->getParam('debug', false)) {
            $export->clear();
        }
        Varien_Profiler::stop('themler::export::_buildExport::clear');
    }

    private function _updateModules()
    {
        $fsoPath = $this->path->themeModuleDir($this->currentTheme);
        if (is_dir($fsoPath)) {
            foreach ($this->fso->enumerate($fsoPath, false, null, true) as $moduleDir) {
                if (is_dir($moduleDir)) {
                    $this->fso->copy($moduleDir, MAGENTO_ROOT);
                }
            }
            $this->fso->remove($fsoPath, true);
        }

        $previewPath = $this->path->themePreviewModuleDir($this->currentTheme);
        if (is_dir($previewPath)) {
            $this->fso->remove($previewPath, true);
        }

        // remove old module
        if (file_exists(MAGENTO_ROOT . '/app/etc/modules/Theme_Designer.xml')) {
            $this->fso->remove(MAGENTO_ROOT . '/app/code/local/Theme', true);
            $this->fso->remove(MAGENTO_ROOT . '/app/etc/modules/Theme_Ajax.xml');
            $this->fso->remove(MAGENTO_ROOT . '/app/etc/modules/Theme_Designer.xml');
            $this->fso->remove(MAGENTO_ROOT . '/skin/adminhtml/default/default/designer', true);
            $this->fso->remove(MAGENTO_ROOT . '/app/design/adminhtml/default/default/template/designer', true);
            $this->fso->remove(MAGENTO_ROOT . '/app/design/adminhtml/default/default/layout/designer.xml');
        }
    }

    private function _updateHashes($hashes)
    {
        /** @var Billiondigital_Themler_Model_Export_Storage_Abstract $hashStorage */
        $hashStorage = Mage::getSingleton('billionthemler/export_storage_hashes', array(
            'theme' => $this->currentTheme
        ));
        $hashStorage->load()->update($hashes);
        $hashStorage->save();
    }

    /**
     * @param array $cache Cache data
     */
    private function _updateCache($cache)
    {
        /** @var Billiondigital_Themler_Model_Export_Storage_Abstract $cacheStorage */
        $cacheStorage = Mage::getSingleton('billionthemler/export_storage_cache', array(
            'theme' => $this->currentTheme
        ));
        $cacheStorage->load()->update($cache);
        $cacheStorage->save();
    }

    private function _updateImages($projectImages)
    {
        $images = array();

        foreach ($projectImages as $imgName => $imgValue) {
            $imgName = preg_replace('/[^a-z0-9_\.]/i', '', $imgName);
            $images[$imgName] = $imgValue;
        }

        foreach ($this->fso->enumerate($this->path->themeSkinDir($this->currentTheme) . '/images/designer') as
                 $path) {
            $name = basename($path);
            if (!array_key_exists($name, $images)) {
                $this->fso->remove($path);
            }
        }

        foreach ($this->fso->enumerate($this->path->previewAppDir($this->currentTheme), false, '#\.phtml$#') as $file) {
            $content = $this->fso->read($file);
            if (content !== null) {
                $content = str_replace(
                    '<?php echo $this->getSkinUrl("images/designer/',
                    '<?php echo Mage::registry(\'templateHelper\')->getSkinUrl(\'images/designer/',
                    $content
                );
                $this->fso->write($file, $content);
            }
        }
    }

    private function _checkPermissions()
    {
        $writable = true;
        foreach (Billiondigital_Core_PermissionException::$themeFolders as $folder) {
            $writable = $writable && is_readable(MAGENTO_ROOT . $folder) && is_writable(MAGENTO_ROOT . $folder);
        }
        return $writable;
    }

}

//