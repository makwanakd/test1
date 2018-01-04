<?php
namespace Icecube\Businesses\Controller\Adoffermanager;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
    	$om = \Magento\Framework\App\ObjectManager::getInstance(); 
 		$customerSession = $om->get('Magento\Customer\Model\Session');
		$storeManager = $om->get('\Magento\Store\Model\StoreManagerInterface');
		$base_url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

	    if($customerSession->isLoggedIn()) {
	            $id = $customerSession->getCustomer()->getId(); 
	            $customerGroupid = $customerSession->getCustomer()->getGroupId(); 
	            if($customerGroupid == 4){	
	               	$this->_view->loadLayout();
			        $this->_view->getLayout()->initMessages();
			        $this->_view->renderLayout();
	            }
	            else{
	            	header('Location: '.$base_url.'login/');
	               	exit();
	            }
	     }else{
	     	header('Location: '.$base_url.'login/');
	        exit();

	     }
    }
    
}