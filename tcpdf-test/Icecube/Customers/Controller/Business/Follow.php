<?php
namespace Icecube\Customers\Controller\Business;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;


class Follow extends Action
{
	protected $resultRedirect;
	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	    /*\Magento\Framework\ObjectManagerInterface $objectmanager,*/
	    /*\Magento\Framework\Controller\ResultFactory $resultRedirect*/
	) {
	    $this->resultJsonFactory = $resultJsonFactory;
	/*    $this->_objectManager = $objectmanager;*/
	   /* $this->resultFactory = $resultRedirect;*/
	    parent::__construct($context);
	}	
	public function execute()
	{
		
		$request = $this->getRequest();
		//$page_id = (int)$request->getParam('page_id');
		$business_id = (int)$request->getParam('id');
		//$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
		if($customerSession->isLoggedIn()) {
		  $customer = $customerSession->getCustomer(); 
		  $logincustomerid = $customer->getId(); 
		}
		//$customer_id = (int)$request->getParam('customer_id');
		$status = 1;
		$follow = $this->_objectManager->create('Icecube\Businesses\Model\Follow');
		$follow//->setData('page_id',
	            //$page_id
	    //)
			->setData('business_id',
	    	$business_id
	    )->setData('customer_id',
	    	$logincustomerid
	    )->setData('status',
	    	$status
        );
        $follow->save();
        $this->messageManager->addSuccess( __('Business followed Successfully.') );
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    	$resultRedirect->setUrl($this->_redirect->getRefererUrl());
    	return $resultRedirect; 
        
	}
}