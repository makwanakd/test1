<?php
namespace Icecube\Businesses\Controller\Locations;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
    
}