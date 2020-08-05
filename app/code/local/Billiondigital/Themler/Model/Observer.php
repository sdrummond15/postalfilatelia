<?php
//

class Billiondigital_Themler_Model_Observer
{
    /**
     * @param $observer
     */
    public function controller_action_predispatch(Varien_Event_Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();

        $themeName = Mage::app()->getRequest()->getParam('theme');

        if (!$themeName) {
            // backward compatibility with new param
            $themeName = $action->getRequest()->getParam('template');
        }
        $isPreview = $action->getRequest()->getParam('preview');

        if ($themeName) {
            $cfg = Mage::getSingleton('billioncore/config');
            $appDir = MAGENTO_ROOT . $cfg->get('paths/app_base');

            $themeDirname = $cfg->get('paths/theme/dirname', array('theme' => $themeName));
            $loadPriority = array($themeDirname);
            if ($isPreview) {
                array_unshift($loadPriority, $cfg->get('paths/preview/dirname', array('theme' => $themeName)));
                Mage::helper('billionthemler/utility')->registerCommonErrorHandler();
            }

            $currentDirname = $themeDirname;

            if (is_readable($appDir . $themeDirname . '/designer/project.json')) {
                do {
                    $currentDirname = current($loadPriority);

                    if (is_dir($appDir . $currentDirname)) {
                        break;
                    }
                } while (next($loadPriority) !== false);
            }

            $actualTheme = basename($currentDirname);
            $package = Mage::getSingleton('core/design_package');
            $package->setTheme($actualTheme);
        }
    }

}

//