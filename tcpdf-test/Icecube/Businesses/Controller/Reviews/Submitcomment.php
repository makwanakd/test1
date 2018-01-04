<?php
namespace Icecube\Businesses\Controller\Reviews;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\Translate\Inline\StateInterface;
use \Magento\Store\Model\StoreManagerInterface;

class Submitcomment extends Action
{
	/*protected $resultRedirect1;*/
	protected $_dateFactory;
	protected $inlineTranslation;
    
    protected $transportBuilder;
	
	protected $storeManager;
	
	protected $_scopeConfig;
	
	protected $_modelFollowFactory;
 	protected $_modelReviewsFactory;
 	protected $_modelCommentsFactory;
	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
	    /*\Magento\Framework\ObjectManagerInterface $objectmanager,*/
	   /* ResultFactory $result,*/
	    \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
	    StateInterface $inlineTranslation,
    	TransportBuilder $transportBuilder,
    	StoreManagerInterface $storeManager,
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    	\Icecube\Businesses\Model\ResourceModel\Follow\CollectionFactory $modelFollowFactory,
        \Icecube\Businesses\Model\ResourceModel\Reviews\CollectionFactory $modelReviewsFactory,
        \Icecube\Businesses\Model\ResourceModel\Comments\CollectionFactory $modelCommentsFactory
	) {
	    $this->resultJsonFactory = $resultJsonFactory;
	/*    $this->_objectManager = $objectmanager;*/
	    parent::__construct($context);
	    $this->_dateFactory = $dateFactory;
	    // $this->resultRedirect1 = $result;
	    
	    $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
	    $this->_scopeConfig = $scopeConfig;
	    $this->_modelFollowFactory = $modelFollowFactory; 
        $this->_modelReviewsFactory = $modelReviewsFactory; 
        $this->_modelCommentsFactory = $modelCommentsFactory;
        
	}
		public function execute(){
			$request = $this->getRequest();
			
			$request = $this->getRequest();
			 $page_id = (int)$request->getParam('cpageid');
			 $business_id = (int)$request->getParam('cbusinessid');
			 $customer_id = (int)$request->getParam('ccustomerid');
			 $ccomment = $request->getParam('ccomment');
			 $creviewid = $request->getParam('creviewid');
			 $status = 1;
			 $date = $this->_dateFactory->create()->gmtDate();
			
			$comment = $this->_objectManager->create('Icecube\Businesses\Model\Comments');
			$comment->setData('page_id',
		            $page_id
		    )->setData('business_id',
		    	$business_id
		    )->setData('review_id',
		    	$creviewid
		    )->setData('customer_id',
		    	$customer_id
		    )->setData('comment',
		    	$ccomment
		    )->setData('status',
		    	$status
	        )->setData('datetime',
		    	$date
	        );

	        $comment->save();
	        
	        $commentId = $comment->getId();
	        $this->sendStatusUpdate($business_id,$ccomment,$customer_id,$creviewid,$commentId);
	        
	        $this->messageManager->addSuccess('Comment submitted Successfully');
	       	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
		    return $resultRedirect;

	        /*$data['success'] = true;
	        $result = $this->resultJsonFactory->create()->setData($data);		 	*/
	}
	public function sendStatusUpdate($id,$text,$currentcustomer,$creviewid,$commentId) {
		
		$model = $this->_objectManager->create('Icecube\Businesses\Model\Businesses')->load($id);
		$BusinessuserId = $model->getBusinessuserId();
		$businessUserDetails = $this->getCustomerDetails($BusinessuserId);
		$businessName = $businessUserDetails->getBusinessName();
		$base_url = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
		$businessUrl = ''.$base_url.'businesses/reviews/view?r='.$creviewid.'-'.$commentId;
		
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
		
		
		$mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$customerObj = $this->getCustomerDetails($currentcustomer);
		$isimageAvailable = $customerObj->getProfileImage();
		$customerName = $customerObj->getName();		
		$profilepic = $mediaUrl.'wysiwyg/user-default.jpg';
		if($customerObj->getId()){
			if($isimageAvailable!=NULL && $isimageAvailable!=''){
				$profilepic = $mediaUrl.$isimageAvailable;			
			}else{
				$profilepic = $mediaUrl.'wysiwyg/user-default.jpg';
			}
		}
		$templateVars = array( 
		                    'store' => $this->storeManager->getStore(),
		                    'reviewtext' => $text,
		                    'profileimage' => $profilepic,
		                    'businessname' => $businessName,
		                    'businessurl' => $businessUrl,
		                    'customer_name' => $customerName
		                );
		                
		$copyTo = $this->getEmailIds($id,$currentcustomer,$creviewid);
		
		$from = array('email' => $this->getSalesEmail(), 'name' => $this->getSalesName());
		$this->inlineTranslation->suspend();
		
		
		foreach($copyTo as $emailid){
			$to = array($emailid);
			$transport = $this->_transportBuilder->setTemplateIdentifier(10)
		                ->setTemplateOptions($templateOptions)
		                ->setTemplateVars($templateVars)
		                ->setFrom($from)
		                ->addTo($to)
		                ->getTransport();
			$transport->sendMessage();
		}
		
		$this->inlineTranslation->resume();		
		return;		
	}	
	public function getSalesEmail()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getSalesName()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_general/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getReviews($business_id)
	{
		$reviewModel = $this->_modelReviewsFactory->create();
		$items = $reviewModel->addFieldToFilter('business_id',$business_id)->load();
        return $items;
	}
	public function getComments($business_id)
	{
		$commentModel = $this->_modelCommentsFactory->create();
		$items = $commentModel->addFieldToFilter('business_id',$business_id)->load();
        return $items;
	}
	public function getCommentsOfReview($business_id,$creviewid)
	{
		$commentModel = $this->_modelCommentsFactory->create();
		$items = $commentModel->addFieldToFilter('review_id',$creviewid)->load();
        return $items;
	}
	public function getReviewOwner($business_id,$creviewid)
	{
		$reviewOwner = $this->_objectManager->create('Icecube\Businesses\Model\Reviews')->load($creviewid);
		return $reviewOwner->getCustomerId();
	}
	public function getFollowers($business_id)
	{
		$followModel = $this->_modelFollowFactory->create();
		$items = $followModel->addFieldToFilter('business_id',$business_id)->load();
        return $items;
	}
	public function getCustomerDetails($customerid)
	{	
		$customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
		$customerData = $customer->load($customerid);
		return $customerData;
	}
	public function getEmailIds($id,$currentcustomer,$creviewid){
		
		$businessUser = $this->_objectManager->create('Icecube\Businesses\Model\Businesses');
		$businessUser->load($id);
		$BusinessuserId = $businessUser->getBusinessuserId();
		
		//$allReviews = $this->getReviews($id);
		//$allComments = $this->getComments($id,$creviewid);
		$allComments = $this->getCommentsOfReview($id,$creviewid);
		$reviewOwner = $this->getReviewOwner($id,$creviewid);
		//$allFollowers = $this->getFollowers($id);
		$customerIds = array();
		$customerIds[] = $BusinessuserId;
		$customerIds[] = $reviewOwner;
		
		$customerEmailIds = array();
		/*foreach($allReviews as $reviews){
			$customerIds[] = $reviews->getCustomerId();
		}*/
		foreach($allComments as $comment){
			$customerIds[] = $comment->getCustomerId();
		}
		/*foreach($allFollowers as $follower){
			$customerIds[] = $follower->getCustomerId();
		}*/
		$customerIds = array_unique($customerIds);
		
		foreach($customerIds as $customerid){
			if($currentcustomer!=$customerid){
				$customer = $this->getCustomerDetails($customerid);
				if($customer->getId()){
					$customerEmailIds[] = $customer->getEmail();
				}
			}			
		}
		return $customerEmailIds;
	}		
	
}