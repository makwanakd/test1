<?php
namespace Icecube\Businesses\Controller\View;

use Magento\Framework\App\Action\Action;

class Follow extends Action
{
	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	    /*\Magento\Framework\ObjectManagerInterface $objectmanager*/
	) {
	    $this->resultJsonFactory = $resultJsonFactory;
	    /*$this->_objectManager = $objectmanager;*/
	    parent::__construct($context);
	}
	public function execute(){
		$request = $this->getRequest();
		$type = $request->getParam('type');
		if($type == 'follow'):
			return $this->follow();
		else:
			return $this->unfollow();
		endif;
	}
	public function follow() {
		
		$request = $this->getRequest();
		$page_id = (int)$request->getParam('page_id');
		$business_id = (int)$request->getParam('business_id');
		$customer_id = (int)$request->getParam('customer_id');
		$status = 1;
		$follow = $this->_objectManager->create('Icecube\Businesses\Model\Follow');
		$follow->setData('page_id',
	            $page_id
	    )->setData('business_id',
	    	$business_id
	    )->setData('customer_id',
	    	$customer_id
	    )->setData('status',
	    	$status
        );
        $follow->save();
        $data['message'] = 'Business Followed Successfully';
        $data['success'] = true;
        $result = $this->resultJsonFactory->create()->setData($data);
	 	return $result;
	}
	public function unfollow()
	{
		$request = $this->getRequest();
		$follow_id = (int)$request->getParam('follow_id');
		$follow = $this->_objectManager->create('Icecube\Businesses\Model\Follow');
		$follow->load($follow_id);
        $follow->delete();
		$data['message'] = 'Business Unfollowed Successfully';
        $data['success'] = true;
        $result = $this->resultJsonFactory->create()->setData($data);
	 	return $result;
	}
}