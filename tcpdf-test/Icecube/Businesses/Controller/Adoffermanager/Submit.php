<?php
namespace Icecube\Businesses\Controller\Adoffermanager;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\Translate\Inline\StateInterface;
use \Magento\Store\Model\StoreManagerInterface;

class Submit extends Action
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
 	protected $_modelAdoffersFactory;
 	protected $userFactory;

	public function __construct(
	    \Magento\Framework\App\Action\Context $context,
	    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
	    /*\Magento\Framework\ObjectManagerInterface $objectmanager,*/
	  /*  ResultFactory $result,*/
	    \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
	    StateInterface $inlineTranslation,
    	TransportBuilder $transportBuilder,
    	StoreManagerInterface $storeManager,
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    	\Icecube\Businesses\Model\ResourceModel\Follow\CollectionFactory $modelFollowFactory,
        \Icecube\Businesses\Model\ResourceModel\Reviews\CollectionFactory $modelReviewsFactory,
        \Icecube\Businesses\Model\ResourceModel\Comments\CollectionFactory $modelCommentsFactory,
        \Icecube\Businesses\Model\ResourceModel\Adoffers\CollectionFactory $modelAdoffersFactory,
        \Magento\User\Model\UserFactory $userFactory    
	) {
	    $this->resultJsonFactory = $resultJsonFactory;
	    /*$this->_objectManager = $objectmanager;*/
	    parent::__construct($context);
	    $this->_dateFactory = $dateFactory;
	    /*$this->resultRedirect = $result; */   
	    $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
	    $this->_scopeConfig = $scopeConfig;
	    $this->_modelFollowFactory = $modelFollowFactory; 
        $this->_modelReviewsFactory = $modelReviewsFactory; 
        $this->_modelCommentsFactory = $modelCommentsFactory;
        $this->_modelAdoffersFactory = $modelAdoffersFactory;      
        $this->storeManager = $storeManager;
        $this->userFactory = $userFactory;	    
	}
		public function execute(){
			/*$this->sendMailoffertouser();	
			die;*/		
			$request = $this->getRequest();
			$page_id = '2';
			/*$request->getParam('coupon_unique_email1');
			$request->getParam('coupon_unique_email2');
			$request->getParam('coupon_unique_email3'); */
			/*$business_id = (int)$request->getParam('businessid');
			$customer_id = (int)$request->getParam('rcustomerid');
			$rrating = (int)$request->getParam('rating');
			$rreview = $request->getParam('txtreview');
			$status = 1;
			$date = $this->_dateFactory->create()->gmtDate();*/
			
			if($request->getParam('adoffferid1')){
				$adoffers = $this->_objectManager->create('Icecube\Businesses\Model\Adoffers');
				
				$uniqueUrl1 = '';
				if($request->getParam('coupon_unique_url1')=='unique-url'){
					$uniqueUrl1 = $this->generateRandomString();
				}else{
					$uniqueUrl1 = $request->getParam('coupon_unique_url1');
				}
				$ismailsend = 0;
				$allowMail = FALSE;
				if($request->getParam('adoffferid1')!='inital'){
					$adoffers->load($request->getParam('adoffferid1'));
					
					if($adoffers->getCouponSendEmail()!='1'){
						if($request->getParam('coupon_unique_email1') == '1'){
							//$this->sendMailoffer(1);
							$allowMail = TRUE;
							$ismailsend = 1;
						}else{
							$ismailsend = 0;
						}	
					}
					else{
						$ismailsend = 1;
					}
				}else{
					if($request->getParam('coupon_unique_email1')==1){
							//$this->sendMailoffer(1);
							$allowMail = TRUE;
							$ismailsend = 1;
						}
				}
				$adoffers->setData('business_id',
			    	$request->getParam('businessid')
			    )->setData('businessuser_id',
			    	$request->getParam('businessuid')
			    )->setData('logo',
			    	$request->getParam('logo1')
			    )->setData('logo_additional',
			    	$request->getParam('logoadditional1')
			    )->setData('targeting_campaign',
			    	$request->getParam('targeting_campaign1')
			    )->setData('campaign_title',
			    	$request->getParam('campaign_title1')
		        )->setData('allocated_inventory',
			    	$request->getParam('allocated_inventory1')
		        )->setData('business_location',
			    	$request->getParam('business_location1')
		        )->setData('target_market',
			    	$request->getParam('target_market1')
		        )->setData('adoffer_headline',
			    	$request->getParam('adoffer_headline1')
		        )->setData('offer_type',
			    	$request->getParam('offer_type1')
		        )->setData('promo_code',
			    	$request->getParam('promo_code1')
		        )->setData('promo_code_img',
			    	$request->getParam('promo_code_img1')
		        )->setData('expiration_date',
			    	$request->getParam('expiration_date1')
		        )->setData('adoffer_description',
			    	$request->getParam('adoffer_description1')
		        )->setData('how_to_redeem',
			    	$request->getParam('how_to_redeem1')
		        )->setData('terms_conditions',
			    	$request->getParam('terms_conditions1')
		        )->setData('coupon_unique_url',
			    	$uniqueUrl1
		        )->setData('coupon_send_email',
			    	$ismailsend
		        );
		        $adoffers->save();
		        if($allowMail){
					$this->sendMailoffer(1,$adoffers->getId());
				}else{
					$this->updateProduct(1,$adoffers->getId());
				}				
			}
			if($request->getParam('adoffferid2')){
				if($request->getParam('adoffferid1')!='inital' && ($request->getParam('campaign_title2')!=NULL || $request->getParam('campaign_title2')!='')){
					$adoffers = $this->_objectManager->create('Icecube\Businesses\Model\Adoffers');
					
					$uniqueUrl2 = '';
					if($request->getParam('coupon_unique_url2')=='unique-url'){
						$uniqueUrl2 = $this->generateRandomString();
					}else{
						$uniqueUrl2 = $request->getParam('coupon_unique_url2');
					}
					$ismailsend = 0;
					$allowMail = FALSE;
					if($request->getParam('adoffferid2')!='inital'){
					$adoffers->load($request->getParam('adoffferid2'));
					
					if($adoffers->getCouponSendEmail()!='1'){
						if($request->getParam('coupon_unique_email2') == '1'){
							//$this->sendMailoffer(2);
							$allowMail = TRUE;
							$ismailsend = 1;
						}else{
							$ismailsend = 0;
						}	
					}
					else{
						$ismailsend = 1;
					}
				}else{
					if($request->getParam('coupon_unique_email2')==1){
							//$this->sendMailoffer(2);
							$allowMail = TRUE;
							$ismailsend = 1;
						}
				}
					$adoffers->setData('business_id',
				    	$request->getParam('businessid')
				    )->setData('businessuser_id',
				    	$request->getParam('businessuid')
				    )->setData('logo',
				    	$request->getParam('logo2')
				    )->setData('logo_additional',
				    	$request->getParam('logoadditional2')
				    )->setData('targeting_campaign',
				    	$request->getParam('targeting_campaign2')
				    )->setData('campaign_title',
				    	$request->getParam('campaign_title2')
			        )->setData('allocated_inventory',
				    	$request->getParam('allocated_inventory2')
			        )->setData('business_location',
				    	$request->getParam('business_location2')
			        )->setData('target_market',
				    	$request->getParam('target_market2')
			        )->setData('adoffer_headline',
				    	$request->getParam('adoffer_headline2')
			        )->setData('offer_type',
				    	$request->getParam('offer_type2')
			        )->setData('promo_code',
				    	$request->getParam('promo_code2')
			        )->setData('promo_code_img',
				    	$request->getParam('promo_code_img2')
			        )->setData('expiration_date',
				    	$request->getParam('expiration_date2')
			        )->setData('adoffer_description',
				    	$request->getParam('adoffer_description2')
			        )->setData('how_to_redeem',
				    	$request->getParam('how_to_redeem2')
			        )->setData('terms_conditions',
				    	$request->getParam('terms_conditions2')
			        )->setData('coupon_unique_url',
				    	$uniqueUrl2
			        )->setData('coupon_send_email',
			    		$ismailsend
		        	);
			        $adoffers->save();
			        if($allowMail){
						$this->sendMailoffer(2,$adoffers->getId());
					}else{
						$this->updateProduct(2,$adoffers->getId());
					}	
				}
							
			}
			
			if($request->getParam('adoffferid3')){
				if($request->getParam('adoffferid2')!='inital' && ($request->getParam('campaign_title3')!=NULL || $request->getParam('campaign_title3')!='')){
					$adoffers = $this->_objectManager->create('Icecube\Businesses\Model\Adoffers');
					
					$uniqueUrl3 = '';
					if($request->getParam('coupon_unique_url3')=='unique-url'){
						$uniqueUrl3 = $this->generateRandomString();
					}else{
						$uniqueUrl3 = $request->getParam('coupon_unique_url3');
					}
					
					$ismailsend = 0;
					$allowMail = FALSE;
					if($request->getParam('adoffferid3')!='inital'){
					$adoffers->load($request->getParam('adoffferid3'));
					
					if($adoffers->getCouponSendEmail()!='1'){
						if($request->getParam('coupon_unique_email3') == '1'){
							//$this->sendMailoffer(3);
							$allowMail = TRUE;
							$ismailsend = 1;
						}else{
							$ismailsend = 0;
						}	
					}
					else{
						$ismailsend = 1;
					}
				}else{
					if($request->getParam('coupon_unique_email3')==1){
							//$this->sendMailoffer(3);
							$allowMail = TRUE;
							$ismailsend = 1;
						}
				}
					$adoffers->setData('business_id',
				    	$request->getParam('businessid')
				    )->setData('businessuser_id',
				    	$request->getParam('businessuid')
				    )->setData('logo',
				    	$request->getParam('logo3')
				    )->setData('logo_additional',
				    	$request->getParam('logoadditional3')
				    )->setData('targeting_campaign',
				    	$request->getParam('targeting_campaign3')
				    )->setData('campaign_title',
				    	$request->getParam('campaign_title3')
			        )->setData('allocated_inventory',
				    	$request->getParam('allocated_inventory3')
			        )->setData('business_location',
				    	$request->getParam('business_location3')
			        )->setData('target_market',
				    	$request->getParam('target_market3')
			        )->setData('adoffer_headline',
				    	$request->getParam('adoffer_headline3')
			        )->setData('offer_type',
				    	$request->getParam('offer_type3')
			        )->setData('promo_code',
				    	$request->getParam('promo_code3')
			        )->setData('promo_code_img',
				    	$request->getParam('promo_code_img3')
			        )->setData('expiration_date',
				    	$request->getParam('expiration_date3')
			        )->setData('adoffer_description',
				    	$request->getParam('adoffer_description3')
			        )->setData('how_to_redeem',
				    	$request->getParam('how_to_redeem3')
			        )->setData('terms_conditions',
				    	$request->getParam('terms_conditions3')
			        )->setData('coupon_unique_url',
				    	$uniqueUrl3
			        )->setData('coupon_send_email',
			    	$ismailsend
		        );
			        $adoffers->save();
			        if($allowMail){
						$this->sendMailoffer(3,$adoffers->getId());
					}else{
						$this->updateProduct(3,$adoffers->getId());
					}
				}
			}
	        $this->messageManager->addSuccess('Offers has been saved successfully');
	       	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
		    return $resultRedirect;
	}	
	public function sendMailoffer($count,$offerId){
		//$count = 1;
		$regionName = '';
		$request = $this->getRequest();

		//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
		if ($customerSession->isLoggedIn()) {
    		$customerId = $customerId = $customerSession->getCustomerId();
    		$customerObj = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
    		$customerFirstName = $customerObj->getFirstname();
    		$customerLastName = $customerObj->getLastname(); 
    		$customerEmail = $customerObj->getEmail();
    		$customerPhone = $customerObj->getPrimaryBillingAddress()->getTelephone(); 
    		$businessName = $customerObj->getBusinessName(); 
    		 $customerObj->getBusinessCategory(); 
    		$category = $this->_objectManager->create('\Icecube\Business\Model\Preferences')->load($customerObj->getBusinessCategory());
    		$businesscategory = $category->getPreferenceTitle(); 
    		// $request->getParam('target_market'); die;
    		$target_market_Id = $request->getParam('target_market'.$count); 
    		$region = $this->_objectManager->create('Magento\Directory\Model\Region')->load($target_market_Id);
	        if ($region) {
	          $regionName = $region->getName(); 
	    	}
    		//$customeradd = $this->customerRepository->getById($customerId);
 			//echo $billingAddressId = $customeradd->getDefaultBilling(); die;
    		//echo $customerPhone = $customerObj->getPhone(); die;

    	}
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());	
		$templateVars = array( 
		                    //'store' => $this->storeManager->getStore(),
		                    'customerid' => $customerId,
		                    'first_name' => 	$customerFirstName,
		                    'last_name' => 	$customerLastName,
		                    'email' => $customerEmail,
		                    'phone' => $customerPhone,
		                    'businessName' => $businessName, 
		                    'businesscategory' => $businesscategory,
		                    'campaign_title' => $request->getParam('campaign_title'.$count), 
		                    'allocated_inventory' => $request->getParam('allocated_inventory'.$count),
		                    'target_market' => $regionName,
		                    'offer_type' => $request->getParam('offer_type'.$count),
		                    'adoffer_headline' => $request->getParam('adoffer_headline'.$count),
		                    'promo_code' => $request->getParam('promo_code'.$count),
		                    'expiration_date' => $request->getParam('expiration_date'.$count),
		                    'adoffer_description' => $request->getParam('adoffer_description'.$count),
		                    'how_to_redeem' => $request->getParam('how_to_redeem'.$count),
		                    'terms_conditions' => $request->getParam('terms_conditions'.$count)

		                );
		//echo"<pre>"; print_r($templateVars); die;
		//die;
		$businessDatas = array_merge($templateVars);
		$from = array('email' => $customerEmail, 'name' => $customerFirstName);
		$this->inlineTranslation->suspend();
		//$to = array('email' => $this->getSalesEmail()); 
		//$to = $this->getSalesEmail();
		//$to = 'test8.capital@gmail.com';
		/*$transport = $this->_transportBuilder->setTemplateIdentifier(13)
		                ->setTemplateOptions($templateOptions)
		                ->setTemplateVars($templateVars)
		                ->setFrom($from)
		                ->addTo($to)
		                ->getTransport();
		                
			$transport->sendMessage();
			$this->inlineTranslation->resume();*/




			$storeId = null;
		$adminEmails = array();
		$adminUsers = $this->userFactory->create()->getCollection();
		
		foreach($adminUsers as $admin){
    		$roleName = $admin->getRole()->getRoleName();
    		$adminEmails[] = array($admin->getEmail());
		}
		foreach($adminEmails as $adminEmail){
	        $transport = $this->_transportBuilder->setTemplateIdentifier(13)
		                ->setTemplateOptions($templateOptions)
		                ->setTemplateVars($templateVars)
		                ->setFrom($from)
		                ->addTo($adminEmail)
		                ->getTransport();
			$transport->sendMessage();
		}
		$this->inlineTranslation->resume();

		//create product for adoffers starts
			$sku = $request->getParam('businessid').$offerId;
			$_product = $this->_objectManager->create('Magento\Catalog\Model\Product');
			$_product->setName($businessName.' - '.$request->getParam('campaign_title'.$count));
			$_product->setTypeId('virtual');
			$_product->setAttributeSetId(4);
			$_product->setStoreId(0);
			$_product->setSku($sku);
			$_product->setWebsiteIds(array(1));
			$_product->setVisibility(4);
			$_product->setPrice(100);
			$_product->setStatus(1);
			/*$_product->setQty(100);
			$_product->setIsInStock(TRUE);*/
			
			
			
			/*$_product->setImage('/testimg/test.jpg');
			$_product->setSmallImage('/testimg/test.jpg');
			$_product->setThumbnail('/testimg/test.jpg');*/
			/*$_product->setStockData(array(
			        'use_config_manage_stock' => 0, //'Use config settings' checkbox
			        'manage_stock' => 1, //manage stock
			        'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
			        'max_sale_qty' => 2, //Maximum Qty Allowed in Shopping Cart
			        'is_in_stock' => 1, //Stock Availability
			        'qty' => 100 //qty
			        )
			    );*/

			$_product->save();
			
			
		$newQty = 100;
		$stockRegistry = $this->_objectManager->get('\Magento\CatalogInventory\Api\StockRegistryInterface');
		$stockItem = $stockRegistry->getStockItemBySku($sku);
	    $stockItem->setQty($newQty);
	    $stockItem->setIsInStock(true);
	    $stockRegistry->updateStockItemBySku($sku, $stockItem);
		    
		//create product for adoffers ends

	}
	public function updateProduct($count,$offerId){
		$regionName = '';
		$request = $this->getRequest();

		$productRepository = $this->_objectManager->get('\Magento\Catalog\Model\ProductRepository'); 
		$sku = $request->getParam('businessid').$offerId;
		
		$customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
		$businessName = '';
		if ($customerSession->isLoggedIn()) {
    		$customerId = $customerSession->getCustomerId();
    		$customerObj = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
    		$businessName = $customerObj->getBusinessName();
		}
		
		
		try {
		    $tmpProduct = $productRepository->get($sku);
			if($tmpProduct){
				if($tmpProduct->getId()){
					if($businessName!=''){
						$_product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($tmpProduct->getId());
						$_product->setName($businessName.' - '.$request->getParam('campaign_title'.$count));
						$_product->save();
					}					
				}
			}
		} catch (\Magento\Framework\Exception\NoSuchEntityException $e){
		    
		}
	}
	public function getSalesEmail()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getSalesName()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    function generateRandomString($length = 15) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
	public function sendMailoffertouser(){	
		$request = $this->getRequest();	
		$offerId = 23; 
		//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$adoffers = $this->_objectManager->create('Icecube\Businesses\Model\Adoffers');
		$adoffers->load($offerId);
		$BusinessuserId = $adoffers->getBusinessuserId();

		$CampaignTitle = $adoffers->getCampaignTitle();

		$AllocatedInventory = $adoffers->getAllocatedInventory();

		$AdofferHeadline = $adoffers->getAdofferHeadline();
		$OfferType = $adoffers->getOfferType();
		$PromoCode = $adoffers->getPromoCode();
		$ExpirationDate = $adoffers->getExpirationDate();
		$AdofferDescription = $adoffers->getAdofferDescription();
		$HowToRedeem = $adoffers->getHowToRedeem();
		$TermsConditions = $adoffers->getTermsConditions();
		$target_id = $adoffers->getTargetMarket(); 
		$region = $this->_objectManager->create('Magento\Directory\Model\Region')->load($target_id); 
        if ($region) {
         $regionName = $region->getName(); 
    	}
		$customerObj = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($BusinessuserId);
		$customerFirstName = $customerObj->getFirstname();
		$customerLastName = $customerObj->getLastname(); 
		$customerName = $customerObj->getName(); 
		$customerEmail = $customerObj->getEmail();
		$customerPhone = $customerObj->getPrimaryBillingAddress()->getTelephone(); 
		$businessName = $customerObj->getBusinessName(); 
		$customerObj->getBusinessCategory(); 
		$category = $this->_objectManager->create('\Icecube\Business\Model\Preferences')->load($customerObj->getBusinessCategory());
		$businesscategory = $category->getPreferenceTitle();
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());	
		$templateVars = array( 
		                    //'store' => $this->storeManager->getStore(),
		                    'customerid' => $BusinessuserId,
		                    'first_name' => 	$customerFirstName,
		                    'last_name' => 	$customerLastName,
		                    'full_name' => $customerName,
		                    'email' => $customerEmail,
		                    'phone' => $customerPhone,
		                    'businessName' => $businessName, 
		                    'businesscategory' => $businesscategory,
		                    'campaign_title' => $CampaignTitle, 
		                    'allocated_inventory' => $AllocatedInventory,
		                    'target_market' => $regionName,
		                    'offer_type' => $OfferType,
		                    'adoffer_headline' => $AdofferHeadline,
		                    'promo_code' => $PromoCode,
		                    'expiration_date' => $ExpirationDate,
		                    'adoffer_description' => $AdofferDescription,
		                    'how_to_redeem' => $HowToRedeem,
		                    'terms_conditions' => $TermsConditions

		                );
		//echo"<pre>"; print_r($templateVars); die;
		$adminEmail = 'test8.capital@gmail.com';
		$adminFirstName = 'admin';
		$from = array('email' => $adminEmail, 'name' => $adminFirstName);
		//$from = array('email' => $customerEmail, 'name' => $customerFirstName);
		$this->inlineTranslation->suspend();
		//$to = array('email' => $this->getSalesEmail()); 
		//$to = $this->getSalesEmail();
		$to = $customerEmail;
		$transport = $this->_transportBuilder->setTemplateIdentifier(14)
		                ->setTemplateOptions($templateOptions)
		                ->setTemplateVars($templateVars)
		                ->setFrom($from)
		                ->addTo($to)
		                ->getTransport();
		                
			$transport->sendMessage();
			$this->inlineTranslation->resume();

	}

}