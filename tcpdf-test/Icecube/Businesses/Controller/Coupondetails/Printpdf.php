<?php
namespace Icecube\Businesses\Controller\Coupondetails;
class Printpdf extends \Magento\Framework\App\Action\Action
{	
	protected $currentCustomer;
	protected $_modelAdoffersFactory;
	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
	    \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
	    \Icecube\Businesses\Model\ResourceModel\Adoffers\CollectionFactory $modelAdoffersFactory
	) {
	    $this->resultJsonFactory = $resultJsonFactory;
	    $this->currentCustomer = $currentCustomer;
	    $this->_modelAdoffersFactory = $modelAdoffersFactory;
	    parent::__construct($context);
	}
    public function getCustomerId(){

          return $this->currentCustomer->getCustomerId();
    }
    public function getCollectionAdOfferByUrl($unique_url,$business_id)
    {
    	$adoffersModel = $this->_modelAdoffersFactory->create();
		$items = $adoffersModel->addFieldToFilter('coupon_unique_url',$unique_url)->addFieldToFilter('business_id',$business_id)
				->setOrder('adoffers_id','ASC')
				->load();
        return $items;
    }
    public function getBusinessId(){
		return $this->getRequest()->getParam('id');
    }
    public function getBusiness(){
		  $id = $this->getRequest()->getParam('id'); 
        if ($id) {
			$model = $this->_objectManager->create('Icecube\Businesses\Model\Businesses');
			$model->load($id);
			return $model;
		}
		return false;
    }
     public function getMediaUrl()
    {
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}
	public function getCustomerDetails($customerid)
	{	
		$customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
		$customerData = $customer->load($customerid);
		return $customerData;
	}
    public function execute()
    {
    	$customerID = $this->getCustomerId();
    	$isCurrentBusinessUser = FALSE;
    	if($customerID==FALSE){
			return;
		}

		$customerGroupId = 4;
		if($customerID){
			$customerInfo = $this->getCustomerDetails($customerID); 
			$customerGroupId = $customerInfo->getGroupId();
		} 
		$coupon_url = $_GET['coupon']; 
		if($coupon_url == ""){
			return;
		}
		$couponCollection = $this->getCollectionAdOfferByUrl($_GET['coupon'],$this->getBusinessId());
		$adoffersId = FALSE;
		foreach($couponCollection as $coupons){
			$adoffersId = $coupons->getId(); 
			if($coupons->getBusinessuserId()==$customerID){
				$customerGroupId = 1;
				$isCurrentBusinessUser = TRUE;
			}
		}
		if($adoffersId==FALSE){
			return;
		}
		if($customerID && $customerGroupId==4){
			return;
		}
		if($customerID && $customerGroupId!=4 && $isCurrentBusinessUser==FALSE){
			$adoffersUpdate = $this->_objectManager->create('Icecube\Businesses\Model\Adoffers')->load($adoffersId);
			$downloads = $adoffersUpdate->getCouponPrinted() + 1;
			$adoffersUpdate->setCouponPrinted($downloads);
			$adoffersUpdate->save();
		}
    }
    
}