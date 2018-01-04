<?php
namespace Icecube\Customers\Controller\Followed;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
 		$customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $baseUrl = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

	    if($customerSession->isLoggedIn()) 
	    {
            $id = $customerSession->getCustomer()->getId(); 
            $customerGroupid = $customerSession->getCustomer()->getGroupId(); 
            if($customerGroupid == 4){
               	header('Location: '.$baseUrl.'profile/settings/');
               	exit();
            }
            else{
                
               	$this->_view->loadLayout();
		       	$this->_view->getLayout()->initMessages();
		       	$this->_view->renderLayout();
            }
	     }else{
     		header('Location: '.$baseUrl.'login/');
        	exit();
	     }
    }
    
}