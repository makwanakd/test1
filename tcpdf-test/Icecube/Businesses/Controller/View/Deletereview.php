<?php
namespace Icecube\Businesses\Controller\View;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
class Deletereview extends Action
{
	/*protected $messageManager;*/
	protected $_modelCommentsFactory;
	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
	    /*\Magento\Framework\ObjectManagerInterface $objectmanager,*/
	    /*\Magento\Framework\Message\ManagerInterface $messageManager,*/
	    \Icecube\Businesses\Model\ResourceModel\Comments\CollectionFactory $modelCommentsFactory
	    
	) {
	    $this->resultJsonFactory = $resultJsonFactory;
	    /*$this->_objectManager = $objectmanager;*/
	   /* $this->messageManager = $messageManager;*/
	    $this->_modelCommentsFactory = $modelCommentsFactory;
	   
	    parent::__construct($context);
	}
	public function execute(){
		$request = $this->getRequest();
		$rid = $request->getParam('review_id');
		$cid = $request->getParam('comment_id');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
 			$customerSession = $objectManager->get('Magento\Customer\Model\Session');
			if($customerSession->isLoggedIn()) {
		        $customer = $customerSession->getCustomer(); 
		        //$customerGroupid = $customer->getGroupId();
		        $customerid = $customer->getId(); 

		    }
			if($rid){
				
			    //echo $customerid; die;
				return $this->deletereview($rid,$customerid);
			}else{
				return $this->deletecomment($cid,$customerid);
			}
		
	}
	public function deletereview($rid,$customerid) {
		$review = $this->_objectManager->create('Icecube\Businesses\Model\Reviews');
		$review->load($rid);
		$businessId = $review->getBusinessId();
		 $statusId = $review->getStatusId(); 
		$model = $this->_objectManager->create('Icecube\Businesses\Model\Businesses');
		$model->load($businessId);
		$BusinessuserId = $model->getBusinessuserId(); 
		if($customerid == $BusinessuserId){
			$items = $this->getComments($rid);
			foreach ($items as $item) {
				$comment = $this->_objectManager->create('Icecube\Businesses\Model\Comments');
				$comment->load($item->getId());
	        	$comment->delete();
			}
			
        	$review->delete();
        	if($statusId){
	        	$status = $this->_objectManager->create('Icecube\Businesses\Model\Timelinestatus');
				$status->load($statusId);
				$status->delete();
        	}
        	
        	$this->messageManager->addSuccess( __('Review successfully deleted.') );
	        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
	       	$resultRedirect->setUrl($this->_redirect->getRefererUrl());
	       	return $resultRedirect;
	       }else{
	       	$this->messageManager->addError(__('You are unauthorize user to perform this action.'));
	       	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
	       	$resultRedirect->setUrl($this->_redirect->getRefererUrl());
	       	return $resultRedirect;
	       }

	}
	public function deletecomment($cid,$customerid) {
		$comment = $this->_objectManager->create('Icecube\Businesses\Model\Comments');
		$comment->load($cid);

		$businessId = $comment->getBusinessId();
		$model = $this->_objectManager->create('Icecube\Businesses\Model\Businesses');
		$model->load($businessId);
		$BusinessuserId = $model->getBusinessuserId(); 
		if($customerid == $BusinessuserId){
			//$comment->addFieldToFilter('customer_id',$customerid)->load();
			$comment->delete();
			$this->messageManager->addSuccess( __('comment successfully deleted.') );
	        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
	       	$resultRedirect->setUrl($this->_redirect->getRefererUrl());
	       	return $resultRedirect;
	       }else{
	       	$this->messageManager->addError(__('You are unauthorize user to perform this action.'));
	       	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
	       	$resultRedirect->setUrl($this->_redirect->getRefererUrl());
	       	return $resultRedirect;
	       }
	}
	public function getComments($rid)	{	
		$commentModel = $this->_modelCommentsFactory->create();		
		$items = $commentModel->addFieldToFilter('review_id',$rid)->load();   
		return $items;	
	}
}
