<?php
//

class Billiondigital_Themler_Controller_AdminAction extends Billiondigital_Core_Controller_AdminAction
{
    protected function _construct() {
        parent::_construct();
        Mage::helper('billionthemler/utility')->registerCommonErrorHandler();
        Mage::helper('billionthemler/utility')->checkMemoryLimit(false);
    }

    protected function setAjaxResponse($data = array()) {
        if ($this->getRequest()->getParam('ajax', false)) {
            $log = array();
            $timers = Varien_Profiler::getTimers();

            foreach ($timers as $name => $timer) {
                $sum = Varien_Profiler::fetch($name, 'sum');
                $count = Varien_Profiler::fetch($name, 'count');
                if ($sum < .0010 && $count < 10) {
                    continue;
                }

                $log[] = array(
                    'key' => '[PHP] ' . $name,
                    'type' => 'start',
                    'time' => (microtime(true) - $sum) * 1000
                );

                $log[] = array(
                    'key' => '[PHP] ' . $name,
                    'type' => 'end',
                    'time' => microtime(true) * 1000
                );
            }

            $data['log'] = $log;
        }

        $this->getResponse()->setBody(
            $this->coreHelper->jsonEncode($data)
        );
    }

    public function preDispatch()
    {
        Mage::getSingleton('core/session', array('name' => 'adminhtml'));

        if ($this->getRequest()->getParam('auto_login', false)) {
            Varien_Profiler::start('themler::auto_login');
            $login = $this->getRequest()->getParam('login', '');
            $pwd = $this->getRequest()->getParam('pwd', '');

            $domain = $this->getRequest()->getParam('domain');
            $startup = $this->getRequest()->getParam('startup');
            $desktop = $this->getRequest()->getParam('desktop');
            $theme = $this->getRequest()->getParam('theme');
            $version = Billiondigital_Themler_Model_Manifest::getThemeVersion($theme);

            $redirectParams = array('theme' => $theme);
            if ($domain) {
                $redirectParams['domain'] = $domain;
            }
            if ($startup) {
                $redirectParams['startup'] = $startup;
            }
            if ($desktop) {
                $redirectParams['desktop'] = $desktop;
            }
            if (strlen($version)) {
                $redirectParams['ver'] = $version;
            }

            if (!Mage::getSingleton('admin/session')->isLoggedIn()) {
                $session = Mage::getSingleton('admin/session');
                $this->getRequest()->setPost('login', array('username' => $login));

                try {
                    /** @var $user Mage_Admin_Model_User */
                    $user = Mage::getModel('admin/user');
                    $user->login($login, $pwd);
                    if ($user->getId()) {
                        $session->renewSession();

                        if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
                            Mage::getSingleton('adminhtml/url')->renewSecretUrls();
                        }
                        $session->setIsFirstPageAfterLogin(true);
                        $session->setUser($user);
                        $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
                    } else {
                        Mage::throwException(Mage::helper('adminhtml')->__('Invalid User Name or Password.'));
                    }
                } catch (Mage_Core_Exception $e) {
                    Mage::dispatchEvent('admin_session_user_login_failed',
                        array('user_name' => $login, 'exception' => $e));
                }
            }

            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);

            Mage::unregister('disableThemlerParams');
            Mage::register('disableThemlerParams', true);

            $url = Mage::helper('core/url')->addRequestParam(
                Mage::getUrl('*/*/*'), array_map('urlencode', $redirectParams)
            );

            Mage::unregister('disableThemlerParams');

            $this->getResponse()->setRedirect($url);

            Varien_Profiler::stop('themler::auto_login');
        } else {
            parent::preDispatch();
        }

        return $this;
    }
}

//