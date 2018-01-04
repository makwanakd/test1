<?php
namespace Icecube\Extrafields\Observer;

use Magento\Framework\Event\ObserverInterface;

class Updateinfo implements ObserverInterface
{
    const CUSTOMER_GROUP_ID = 2;

    protected $_customerRepositoryInterface;
    protected $_request;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$customer = $observer->getEvent()->getCustomer();
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		
    	$preferences = $this->_request->getParam('hdnpreferences');
    	$preferences = explode(',',$preferences);
    	$preference1 = 0;
    	$preference2 = 0;
    	$preference3 = 0;
    	if(!empty($preferences)){
    		if(isset($preferences[0])){
				if($preferences[0]!='' && $preferences[0]!=NULL){
					$preference1 = $preferences[0];
				}
			}
			if(isset($preferences[1])){
				if($preferences[1]!='' && $preferences[1]!=NULL){
					$preference2 = $preferences[1];
				}
			}
			if(isset($preferences[2])){
				if($preferences[2]!='' && $preferences[2]!=NULL){
					$preference3 = $preferences[2];
				}
			}
			$sqlPreferences = "Insert Into mgsh_customer_preferences (customer_id,preference1,preference2,preference3) Values (".$customer->getId().",".$preference1.",".$preference2.",".$preference3.")";
			$connection->query($sqlPreferences);
		}
        
        /*echo $customer->getId();
        die();*/
        /*if ($customer->getGroupId() == 1) {
            $customer->setGroupId(self::CUSTOMER_GROUP_ID);
            $this->_customerRepositoryInterface->save($customer);;
        }*/
    }
}