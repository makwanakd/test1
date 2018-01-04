<?php
namespace Icecube\Businesses\Controller\Coupondetails;
class Download extends \Magento\Framework\App\Action\Action
{	
	protected $currentCustomer;
	protected $_modelAdoffersFactory;
	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
	    \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
	    \Icecube\Businesses\Model\ResourceModel\Adoffers\CollectionFactory $modelAdoffersFactory
	    /*\Magento\Framework\ObjectManagerInterface $objectmanager*/
	) {
	    $this->resultJsonFactory = $resultJsonFactory;
	    /*$this->_objectManager = $objectmanager;*/
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
    	$storeManager = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface');
    	$base_url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    	
    	$customerID = FALSE;
    	$customerID = $this->getCustomerId();
    	$isCurrentBusinessUser = FALSE;
    	if($customerID==FALSE){
			header('Location: '.$base_url.'');
			exit();
		}

		$customerGroupId = 4;
		if($customerID){
			$customerInfo = $this->getCustomerDetails($customerID); 
			$customerGroupId = $customerInfo->getGroupId();
		} 
		$urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
		 $urlInterface->getCurrentUrl(); 
		
		$coupon_url = $_GET['coupon']; 
		if($coupon_url == ""){
			header('Location: '.$base_url.'');
			exit();
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
			header('Location: '.$base_url.'');
			exit();
		}
		if($customerID && $customerGroupId==4){ 
			header('Location: '.$base_url.'');
			exit();
		}
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$coupon = $objectManager->create('Icecube\Businesses\Model\Adoffers'); 
		$couponDetail = $coupon->load($adoffersId);
		 $businessuserId =  $couponDetail->getBusinessuserId(); 
		if($businessuserId){
			$customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($businessuserId);
			$businessName = $customerObj->getBusinessName();
		}
		if($couponDetail->getBusinessId()){
			$businessDetail = $objectManager->create('Icecube\Businesses\Model\Index');
			$businessDetail->load($couponDetail->getBusinessId(), 'business_id');
			$companyDescription = $businessDetail->getCompanyDescription(); 
			//echo $couponDetail->getBusinessId(); die;
		}
		if($couponDetail->getBusinessLocation()){
		$address = $objectManager->create('Magento\Customer\Model\Address')->load($couponDetail->getBusinessLocation());
		//echo $address->getStreet(); die;
		$street = $address['street']; 
		$arr = explode("\n", $street);
		  	$street1 = '';
		  	$street2 = '';
		  	if(isset($arr['0'])){
		   	 	$street1 = $arr['0'];
		  	}
		  	if(isset($arr['1'])){
		    	$street2 = $arr['1']; 
		  	}	
		}
		$currentBusiness = $this->getBusiness();
		$currentBusinessUrl = $currentBusiness->getBusinessPageUrl(); 
		$couponHeading = $couponDetail->getAdofferHeadline(); 
		$couponDescription = $couponDetail->getAdofferDescription();
		$addressLocationName = $address->getLocationName(); 
		$addressCity = $address->getCity(); 
		$addressRegion = $address->getRegion(); 
		$addressPostCode = $address->getPostcode(); 
		$addressTelephone = $address->getTelephone();
		if($address->getWebsiteName()){
		 	$phone_website = $addressTelephone .' | '.$address->getWebsiteName();
		} 
	  	else{
	  		$phone_website = $address->getTelephone();	
	  	}
	   	$PromoCodeImg = $couponDetail->getPromoCodeImg();
	   	$offerType = '';
	   	if($couponDetail->getOfferType() == "Online, In-Store, Both?") {
 			$offerType = "Valid In-Store &amp; Online"; 
		}
 		if($couponDetail->getOfferType() == "In-Store") {
 			$offerType = "Valid In-Store"; 
		}
		if($couponDetail->getOfferType() == "Online") {
 			$offerType = "Valid Online"; 
		}
		$secondStreet = '';
		if($street2 != ''){
	  		$secondStreet = '<tr><td style="margin: 0px; padding: 0px; text-align: left; color: #3a3635; font-size: 12px; font-weight: normal;">'.$street2.'</td></tr>';
		}else{
			$secondStreet = '';
		}

		
		if($customerID && $customerGroupId!=4 && $isCurrentBusinessUser==FALSE){
			$adoffersUpdate = $objectManager->create('Icecube\Businesses\Model\Adoffers')->load($adoffersId);
			$downloads = $adoffersUpdate->getCouponDownloaded() + 1;
			$adoffersUpdate->setCouponDownloaded($downloads);
			$adoffersUpdate->save();
		}

    	$tcpdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$tcpdf->setPrintHeader(false);
			$tcpdf->setPrintFooter(false);	
		$text = '<div class="pdfclass" id="pdfclass">
	<table style="width: 500px; margin: 40px auto 0;">
		<tbody>
		<table style="border: 1px dashed #ddd; width: 93%; border-radius: 5px; float: left; padding: 0px 15px;">
			<tr style="float: left;">
				<td style="width: 80%; margin-right: 10px; float: left;">
					<table>
						<tbody>
							<tr>
							<br>
								<td style="color: #221e1f; display: block; font-size: 20px; font-weight: bolder; height: auto; letter-spacing: 0; line-height: 25px; margin: 25px 0 30px; text-align: left; width: 100%;">'.$couponHeading.'</td>
							</tr>
							<tr>
							<br>
								<td style="color: #221e1f; width: 100%; height: auto; margin: 0; padding: 0px; display: block; text-align: left; line-height: 18px; font-size: 12px; font-weight: normal;">'.$couponDescription.'</td>
							</tr>

							<tr>
							<br>
								<td style="color: #221e1f; font-size: 16px; font-weight: bold; margin: 35px 0 0; padding: 0; text-align: left; float: left;">'.$addressLocationName.'</td>
							</tr>
							<tr>
							<br>
								<td style="margin: 0px; padding: 0px; text-align: left; color: #3a3635; font-size: 12px; font-weight: normal;">'.$street1.'</td>
							</tr>
							'.$secondStreet.'
							<tr>
								<td style="margin: 0px; padding: 0px; text-align: left; color: #3a3635; font-size: 12px; font-weight: normal;">
									'.$addressCity.', '.$addressRegion.''.$addressPostCode.'							</td>
							</tr>
							<tr style="margin: 15px 0 30px; float:left;">
							<br>
								<td style="margin: 0px; padding: 0px; text-align: left; color: #3a3635; font-size: 13px; font-weight: bold;">
									'.$phone_website.'							</td>
							</tr>



						</tbody>
					</table>
					
				</td>
				
					
				<td style="width: 30%; float:right; text-align: center; background-color:#221e1f; -webkit-print-color-adjust: exact; color-adjust: exact; " bgcolor="#221e1f">
					<table  style="width: 100%; padding: 10px 0px;">
						<tbody><tr> 
								<td>
									<img src="'.$base_url.'pub/media/'.$PromoCodeImg.'" alt="">
								</td>
							</tr>
							<tr>
								<td style="width: 100%; float:right; color: #f58220; font-size: 18px; font-weight: bold; letter-spacing: 0; margin: 15px 0; padding: 0; text-align: center;">

									Promo Code: '.$couponDetail->getPromoCode().'							
								</td>
							</tr>
							<tr>
								<td style="width: 100%; float:right; margin: 0px 0px 60px; padding: 0px; font-size: 14px; text-align: center; color: #ffffff; font-weight: 300;">
									<div style="color: #ffffff;">
										<font color="#ffffff">
											'.$offerType.'																							</font>
									</div>
								</td>
							</tr>
							<tr>
								<td style="width: 100%; float:right; text-align: center"><img src="'.$base_url.'pub/media/wysiwyg/qr_logo.png" alt=""></td>
							</tr>
						</tbody></table>
				</td>
				
			</tr>
		</table>	
		</tbody>
	</table>

		<div style="margin: 30px 0px 0px 0px; float: left;">
		
			<h2 style="font-size: 16px; font-weight: bold; text-align: left; color: #221e1f; display: inline-block; margin: 0px 0px 25px 0px; padding: 0px; ">
				How to Redeem This Offer
			</h2>
			<div style="float: left; font-size: 12px; font-weight: normal; text-align: left; color: #221e1f; display: block; margin: 0px 0px 35px 0px; padding: 0px; line-height: 18px;">'.$couponDetail->getHowToRedeem().'
			
			</div>
			<h2 style="font-size: 16px; font-weight: bold; text-align: left; color: #221e1f; display: inline-block; margin: 0px 0px 25px 0px; padding: 0px; ">
				Terms &amp; Conditions
			</h2>
			<div style="font-size: 12px; font-weight: normal; text-align: left; color: #221e1f; display: block; margin: 0px 0px 35px 0px; padding: 0px; line-height: 18px;">'.$couponDetail->getTermsConditions().'
			</div>
			<h2 style="font-size: 16px; font-weight: bold; text-align: left; color: #221e1f; display: inline-block; margin: 0px 0px 25px 0px; padding: 0px; ">
				About '.$businessName.'
			</h2>
			<div style="font-size: 12px; font-weight: normal; text-align: left; color: #221e1f; display: block; margin: 0px 0px 35px 0px; padding: 0px; line-height: 18px;">'.$companyDescription.'
			</div>
		</div>
							




</div>';
		$tcpdf->AddPage();
		$tcpdf->writeHTML($text, true, false, true, false, '');
		$tcpdf->lastPage();
		$fn = 'coupon_'.time().'.pdf';
		//$tcpdf->Output($fn, 'F');
		$tcpdf->Output($fn, 'D');
		
    	/*$this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();*/
    }
    
}