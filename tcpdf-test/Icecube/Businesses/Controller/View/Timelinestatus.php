<?php
namespace Icecube\Businesses\Controller\View;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\Translate\Inline\StateInterface;
use \Magento\Store\Model\StoreManagerInterface;

class Timelinestatus extends Action
{
	protected $_fileUploaderFactory;
 	
 	protected $subDir = 'business/status/';
 	
 	protected $fileSystem;
 	
 	protected $resultJsonFactory;
 	
 	protected $urlBuilder;
 	
 	/*protected $objectmanager;*/
 	
 	protected $_dateFactory;
 	
 	protected $inlineTranslation;
    
    protected $transportBuilder;
	
	protected $storeManager;
	
	protected $_scopeConfig;
	
	protected $_modelFollowFactory;
 	protected $_modelReviewsFactory;
 	protected $_modelCommentsFactory;
	
	public function __construct(
	    \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
	    /*\Magento\Framework\ObjectManagerInterface $objectmanager,*/
	    Filesystem $fileSystem,
	    /*UrlInterface $urlBuilder,*/
	    \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
	    StateInterface $inlineTranslation,
    	TransportBuilder $transportBuilder,
    	StoreManagerInterface $storeManager,
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    	\Icecube\Businesses\Model\ResourceModel\Follow\CollectionFactory $modelFollowFactory,
        \Icecube\Businesses\Model\ResourceModel\Reviews\CollectionFactory $modelReviewsFactory,
        \Icecube\Businesses\Model\ResourceModel\Comments\CollectionFactory $modelCommentsFactory
	) {
		/*$this->_urlBuilder = $urlBuilder;*/
	 	$this->_filesystem = $fileSystem;
	    $this->_fileUploaderFactory = $fileUploaderFactory;
	    $this->resultJsonFactory = $resultJsonFactory;
	 /*   $this->_objectManager = $objectmanager;*/
	    $this->_dateFactory = $dateFactory;
	    
	    $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
	    $this->_scopeConfig = $scopeConfig;
	    $this->_modelFollowFactory = $modelFollowFactory; 
        $this->_modelReviewsFactory = $modelReviewsFactory; 
        $this->_modelCommentsFactory = $modelCommentsFactory;
	    parent::__construct($context);
	}
	 
	public function execute() {
		$request = $this->getRequest();
		$id = (int)$request->getParam('id');
		$type = (int)$request->getParam('type');
		$text = $request->getParam('text');
		$img = $request->getParam('imageuploaded');
		$businessUserId = $request->getParam('business_user');
		if($img != 0):
	        $uploader = $this->_fileUploaderFactory->create(['fileId' => 'status-image']);
		    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
		    $uploader->setAllowRenameFiles(true);
	        $uploader->setFilesDispersion(true);
	        $uploader->setAllowCreateFolders(true);
		    $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($this->subDir.'images/');
		    $filename = $uploader->save($path)['file'];
		    $data['image'] = $this->getMediaUrl($this->subDir).$filename;
		    $save = $this->getSaveUrl($this->subDir).$filename;
		    $data['date'] = $this->setSave($id,$save,$text,$type,$businessUserId);
		    $result = $this->resultJsonFactory->create()->setData($data);
		 	return $result;
		 else:
		 	$this->setSave($id,'',$text,$type,$businessUserId);
		 	$data['image'] = "Image Not Uploaded";
		    $result = $this->resultJsonFactory->create()->setData($data);
		 	return $result;
		 endif;
	}
	public function getMediaUrl($dir)
    {
        return $this->_url->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]).$dir.'images';
    }
    public function getSaveUrl($dir)
    {
		return $dir.'images';
	}
	private function setSave($id,$save,$text,$type,$businessUserId)
	{
		$template = $this->_objectManager->create('Icecube\Businesses\Model\Timelinestatus');
		$today = date('Y-m-d H:i:s');
		$template->setData('text',
	    	$text
	    )->setData('date_time',
	    	$today
	    )->setData('image',
	    	$save
	    )->setData('type',
	    	$type
	    )->setData('business_id',
	    	$id
        );
        $template->save();
        
        
        $date = $this->_dateFactory->create()->gmtDate();			
		$review = $this->_objectManager->create('Icecube\Businesses\Model\Reviews');
		$review->setData('business_id',
	    	$id
	    )->setData('status',
	    	'1'
        )->setData('datetime',
	    	$date
        )->setData('status_id',
	    	$template->getId()
        )
		->setData('customer_id',
	    	$businessUserId
        );
        $review->save();
        $reviewId = $review->getId();
        $this->sendStatusUpdate($id,$save,$text,$type,$businessUserId,$reviewId);
        return $today;
	}
	public function sendStatusUpdate($id,$save,$text,$type,$businessUserId,$reviewId) {
		
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
		
		$mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
		
		$customerObj = $this->getCustomerDetails($businessUserId);
		$isimageAvailable = $customerObj->getProfileImage();
		$customerName = $customerObj->getName();
		$businessName = $customerObj->getBusinessName();
		$businessUrl = ''.$baseUrl.'businesses/reviews/view?r='.$reviewId;
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
		$copyTo = $this->getEmailIds($id,$businessUserId);
		
		$from = array('email' => $this->getSalesEmail(), 'name' => $this->getSalesName());
		$this->inlineTranslation->suspend();
		
		
		foreach($copyTo as $emailid){
			$to = array($emailid);
			if($type=='1'){
				$transport = $this->_transportBuilder->setTemplateIdentifier(11)
		                ->setTemplateOptions($templateOptions)
		                ->setTemplateVars($templateVars)
		                ->setFrom($from)
		                ->addTo($to)
		                ->getTransport();
			}else{
				$transport = $this->_transportBuilder->setTemplateIdentifier(8)
		                ->setTemplateOptions($templateOptions)
		                ->setTemplateVars($templateVars)
		                ->setFrom($from)
		                ->addTo($to)
		                ->getTransport();
			}
			
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
	public function getEmailIds($id,$businessUserId){
		/*$allReviews = $this->getReviews($id);
		$allComments = $this->getComments($id);*/
		$allFollowers = $this->getFollowers($id);
		$customerIds = array();
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
			if($businessUserId!=$customerid){
				$customer = $this->getCustomerDetails($customerid);
				if($customer->getId()){
					$customerEmailIds[] = $customer->getEmail();
				}
			}			
		}
		return $customerEmailIds;
	}
		
}