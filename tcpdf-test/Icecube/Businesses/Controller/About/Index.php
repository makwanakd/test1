<?php
namespace Icecube\Businesses\Controller\About;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
    	$request = $this->getRequest();
		$business_id = (int)$request->getParam('id');		
		
		$identifier = trim($request->getPathInfo(), '/');
    	$followurl = FALSE;
    	$unfollowurl = FALSE;
    	if(strpos($identifier, 'about/follow') !== FALSE){
			$followurl = TRUE;
		}
		if(strpos($identifier, 'about/unfollow') !== FALSE){
			$unfollowurl = TRUE;
		}
		
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$customerSession = $objectManager->get('Magento\Customer\Model\Session');
		$logincustomerid = '';
		$logincustomergroup = '';
		if($customerSession->isLoggedIn()) {
		  $customer = $customerSession->getCustomer(); 
		   $logincustomerid = $customer->getId(); 
		   $logincustomergroup = $customer->getGroupId();
		}
		if($logincustomerid!='' && $logincustomergroup!=4){
			
			$followModel = $objectManager->get('Icecube\Businesses\Model\ResourceModel\Follow\CollectionFactory')->create();
			$items = $followModel->addFieldToFilter('business_id',$business_id)->addFieldToFilter('customer_id',$logincustomerid)->load();
			$followedId = '';
	        foreach($items as $item){
				$followedId = $item->getId();
			}
			$follow = $objectManager->create('Icecube\Businesses\Model\Follow');
			if($unfollowurl){
				if($followedId!=''){
					$follow->load($followedId);
					$followed_customer = $follow->getCustomerId(); 
					if($logincustomerid == $followed_customer){
			        	$follow->delete();
					}
				}
			}
			if($followurl){
				if($followedId==''){
					$status = 1;
					$follow->setData('business_id',
				    	$business_id
				    )->setData('customer_id',
				    	$logincustomerid
				    )->setData('status',
				    	$status
			        );
			        $follow->save();
				}
			}
		}		
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
    
}