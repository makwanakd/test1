<?php
namespace Icecube\Customers\Controller\Business;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;


class Unfollow extends Action
{
	protected $resultRedirect;
	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	   /* \Magento\Framework\ObjectManagerInterface $objectmanager,*/
	    /*\Magento\Framework\Controller\ResultFactory $resultRedirect*/
	) {
	    $this->resultJsonFactory = $resultJsonFactory;
	    /*$this->_objectManager = $objectmanager;*/
	   /* $this->resultFactory = $resultRedirect;*/
	    parent::__construct($context);
	}	
	public function execute()
	{
		
		$post = $this->getRequest()->getParams();
		if($post){
			$follow_id = $post['id']; 

			//$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
			$customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
			if($customerSession->isLoggedIn()) {
			  $customer = $customerSession->getCustomer(); 
			   $logincustomerid = $customer->getId(); 
			}
			$follow = $this->_objectManager->create('Icecube\Businesses\Model\Follow');
			$follow->load($follow_id);
			$followed_customer = $follow->getCustomerId(); 
			if($logincustomerid == $followed_customer){
	        	$follow->delete();  	
				$this->messageManager->addSuccess( __('Business Unfollowed Successfully.') ); 
			}else{
				$this->messageManager->addError( __('Unauthorized User.') ); 
			}
        	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        	$resultRedirect->setUrl($this->_redirect->getRefererUrl());
        	return $resultRedirect; 
        }
	}
}