<?php
//

class Billiondigital_Theme_Model_Observer
{
    public function controller_action_predispatch(Varien_Event_Observer $observer)
    {
        Mage::unregister('templateHelper');
        Mage::register('templateHelper', Mage::helper('billiontheme'));
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function cms_page_render(Varien_Event_Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();

        $actionName = strtolower($action->getFullActionName());
        $action->getLayout()->getUpdate()->addHandle($actionName . '_after');
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function controller_action_layout_render_before(Varien_Event_Observer $observer)
    {
        $layout = Mage::app()->getLayout();

        if ($block = $layout->getMessagesBlock()) {
            $messages = $block->getMessageCollection();
            $layout->unsetBlock('messages');
            $layout->addBlock('billiontheme/messages', 'messages')->setMessages($messages);
        }

        if ($globalMessage = $layout->getBlock('global_messages')) {
            $messages = $globalMessage->getMessageCollection();
            $layout->unsetBlock('global_messages');
            $layout->addBlock('billiontheme/messages', 'global_messages')->setMessages($messages);
        }
    }

}

//