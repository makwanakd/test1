<?php
namespace Icecube\Businesses\Controller\Reviews;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\Translate\Inline\StateInterface;
use \Magento\Store\Model\StoreManagerInterface;

class View extends Action
{
	/*protected $resultRedirect;*/
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
	   /* $this->_objectManager = $objectmanager;*/
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
			$reviewurl = $request->getParam('r');
			$reviewdata = $request->getParam('r');
			$reviewdata = explode('-',$reviewdata);
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
			$baseUrl = $storeManager->getStore()->getBaseUrl();
			if(!$request->getParam('r')){
				header('Location: '.$baseUrl.'');
				exit;
			}
			$reviewId = '';
			$commentId = '';
			if(isset($reviewdata[0])){
				$reviewId = $reviewdata[0];
			}
			if(isset($reviewdata[1])){
				$commentId = $reviewdata[1];
			}
			/*echo $reviewId;
			echo $commentId;*/
			
			$review = $this->_objectManager->create('Icecube\Businesses\Model\Reviews')->load($reviewId);
			
			if(!$review->getBusinessId()){
				header('Location: '.$baseUrl.'');
				exit;
			}
			$model = $this->_objectManager->create('Icecube\Businesses\Model\Businesses')->load($review->getBusinessId());
			
			$Businessurl = $model->getBusinessPageUrl();
			
			$allreviews = $this->getReviews($review->getBusinessId());
			$counter = 0;
			foreach($allreviews as $reviews){
				$counter++;
				if($reviews->getId()==$reviewId){					
					break;
				}
			}
			$tmppage = (int) ($counter / 10);
			$reminder = $counter % 10;
			if($reminder!=0){
				$currentpage = $tmppage + 1;
			}else{
				$currentpage = $tmppage;
			}
			if($currentpage==1){
				$finalurl = ''.$baseUrl.'businesses/'.$Businessurl.'#'.$reviewurl;
			}else{
				$finalurl = ''.$baseUrl.'businesses/'.$Businessurl.'?p='.$currentpage.'#'.$reviewurl;
			}
			header('Location: '.$finalurl);
			exit;
	}
    public function getReviews($business_id)
	{
		$reviewModel = $this->_modelReviewsFactory->create();
		$items = $reviewModel->addFieldToFilter('business_id',$business_id)->setOrder('businessreview_id','DESC')->load();
        return $items;
	}	
}