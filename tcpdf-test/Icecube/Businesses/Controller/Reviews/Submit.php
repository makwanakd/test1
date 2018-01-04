<?php
namespace Icecube\Businesses\Controller\Reviews;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\Translate\Inline\StateInterface;
use \Magento\Store\Model\StoreManagerInterface;

class Submit extends Action
{
	protected $resultRedirect;
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
	    /*ResultFactory $result,*/
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
	  /*  $this->_objectManager = $objectmanager;*/
	    parent::__construct($context);
	    $this->_dateFactory = $dateFactory;
	    /*$this->resultRedirect = $result;*/
	    
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
			$type = $request->getParam('rating');
			/*var_dump($request->getParam('rating'));
			var_dump($request->getParam('txtreview'));
			var_dump($request->getParam('rcustomerid'));
			var_dump($request->getParam('rbusinessid'));
			var_dump($request->getParam('rpageid'));*/
			
			$request = $this->getRequest();
			 $page_id = (int)$request->getParam('rpageid');
			 $business_id = (int)$request->getParam('rbusinessid');
			 $customer_id = (int)$request->getParam('rcustomerid');
			 $rrating = (int)$request->getParam('rating');
			 $rreview = $request->getParam('txtreview');
			 $status = 1;
			 $date = $this->_dateFactory->create()->gmtDate();
			
			$review = $this->_objectManager->create('Icecube\Businesses\Model\Reviews');
			$review->setData('page_id',
		            $page_id
		    )->setData('business_id',
		    	$business_id
		    )->setData('customer_id',
		    	$customer_id
		    )->setData('review',
		    	$rreview
		    )->setData('rating',
		    	$rrating
		    )->setData('status',
		    	$status
	        )->setData('datetime',
		    	$date
	        );

	        $review->save();
	        $reviewId = $review->getId();
	        
	        $this->sendStatusUpdate($business_id,$rrating,$rreview,$customer_id,$reviewId);
	        
	        $this->messageManager->addSuccess('Review submitted Successfully');
	       	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
		    return $resultRedirect;

	        /*$data['success'] = true;
	        $result = $this->resultJsonFactory->create()->setData($data);		 	*/
	}
	public function sendStatusUpdate($id,$rrating,$text,$currentcustomer,$reviewId) {
		if($rrating==NULL || $rrating==''){
			$rrating = 0;
		}
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
		 $base_url = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
		
		$model = $this->_objectManager->create('Icecube\Businesses\Model\Businesses')->load($id);
		$BusinessuserId = $model->getBusinessuserId();
		$businessUserDetails = $this->getCustomerDetails($BusinessuserId);
		$businessName = $businessUserDetails->getBusinessName();
		$businessUrl = ''.$base_url.'businesses/reviews/view?r='.$reviewId;
		
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
		                    'ratings' => $rrating*20,
		                    'profileimage' => $profilepic,
		                    'businessname' => $businessName,
		                    'businessurl' => $businessUrl,
		                    'customer_name' => $customerName
		                );
		                
		$copyTo = $this->getEmailIds($id,$currentcustomer);
		
		$from = array('email' => $this->getSalesEmail(), 'name' => $this->getSalesName());
		$this->inlineTranslation->suspend();
		
		
		foreach($copyTo as $emailid){
			$to = array($emailid);
			$transport = $this->_transportBuilder->setTemplateIdentifier(9)
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
	public function getEmailIds($id,$currentcustomer){
		
		$businessUser = $this->_objectManager->create('Icecube\Businesses\Model\Businesses');
		$businessUser->load($id);
		$BusinessuserId = $businessUser->getBusinessuserId();
		
		//$allReviews = $this->getReviews($id);
		//$allComments = $this->getComments($id);
		$allFollowers = $this->getFollowers($id);
		$customerIds = array();
		$customerIds[] = $BusinessuserId;
		
		$customerEmailIds = array();
		/*foreach($allReviews as $reviews){
			$customerIds[] = $reviews->getCustomerId();
		}*/
		/*foreach($allComments as $comment){
			$customerIds[] = $comment->getCustomerId();
		}*/
		foreach($allFollowers as $follower){
			$customerIds[] = $follower->getCustomerId();
		}
		$customerIds = array_unique($customerIds);
		
		foreach($customerIds as $customerid){
			$customer = $this->getCustomerDetails($customerid);
			if($customer->getId()){
				$customerEmailIds[] = $customer->getEmail();
			}
		}
		return $customerEmailIds;
	}	
}