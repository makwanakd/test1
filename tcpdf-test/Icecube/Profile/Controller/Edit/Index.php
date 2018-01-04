<?php
namespace Icecube\Profile\Controller\Edit;

use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
class Index extends \Magento\Framework\App\Action\Action
{
   	protected $customerRepositoryInterface;
	protected $storeManager;
	protected $customerSession;
	protected $addressRepository;
	/*protected $objectManager;*/
	/*protected $messageManager;
	protected $resultRedirect;*/
	protected $customerRegistry;
	protected $encryptor;
	
    public function __construct(
        Context $context,       
        \Magento\Customer\Model\Session $customerSession,
        StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
       /* \Magento\Framework\ObjectManagerInterface $objectManager,*/
       /* \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\ResultFactory $resultRedirect,*/
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    ) {

        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->Session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->CustomerRepositoryInterface = $customerRepositoryInterface;
        $this->addressRepository = $addressRepository;
        /*$this->_objectManager = $objectManager;*/
     /*   $this->messageManager = $messageManager;
        $this->resultFactory = $resultRedirect;*/
        $this->customerRegistry = $customerRegistry;
        $this->encryptor = $encryptor;
    }

    public function execute()
    {
    	$businessstreet = array();
    	$businessstreet2 = array();
    	$businessstreet3 = array();
    	$businessSA22 = "";
    	$businessSA12 = "";
    	$businessSA13 = "";
    	$businessSA23 = "";
    	$totaladdress1 = "";
    	$totaladdress2 = "";
    	$totaladdress3 = "";
    	$tagline = "";
		$MainWebsiteUrl = "";
		$companyDescription = "";
    	$customerId = $this->Session->getCustomer()->getId();
    	$resultRedirect = $this->resultRedirectFactory->create();
    	//echo $customerId; die;
    	$post = $this->getRequest()->getParams();
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $baseUrl = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB); 
    	//echo"<pre>"; print_r($post); die;
    	if($post){
    		if(isset($post['total-customer-address'])){
				 $totaladdress1 = $post['total-customer-address'];
			}
			if(isset($post['total-customer-address2'])){
				$totaladdress2 = $post['total-customer-address2'];
			}
			if(isset($post['total-customer-address3'])){
				$totaladdress3 = $post['total-customer-address3'];
			}
			if(isset($post['tag-line'])){
				$tagline = $post['tag-line'];
			}
			if(isset($post['main-wedsite-url'])){
				$mainwebsiteurl = $post['main-wedsite-url'];
			}
			if(isset($post['company-description'])){
				$companyDescription = $post['company-description'];
			}

    		if(isset($post['business-fname'])){
				$firstname = $post['business-fname'];
			}
			if(isset($post['total-customer-address'])){
				$totaladdress = $post['total-customer-address'];
			}
			if(isset($post['business-lname'])){
				$lastname = $post['business-lname'];
			}
			
			if(isset($post['business-email'])){
				$businessEmail = $post['business-email'];
			}
			if(isset($post['business-name'])){
				$businessName = $post['business-name'];
			}
			if(isset($post['business-phonenumber'])){
				$businessPhonenumber = $post['business-phonenumber'];
			}
			if(isset($post['business-category'])){
				$businessCategory = $post['business-category'];
			}
			/*if(isset($post['password'])){
				$businessPassword = $post['password'];
			}	*/		
			if(isset($post['business-location-name'])){
				$businessLocationName = $post['business-location-name'];
			}
			if(isset($post['business-street-address-1'])){
				$businessSA1 = $post['business-street-address-1'];
			}
			if(isset($post['business-street-address-2'])){
				$businessSA2 = $post['business-street-address-2'];
				/*if($businessSA2!=''){
					$businessSA1 .= "\n".$businessSA2;
				}*/
				//echo $businessSA1; die;
				$businessstreet = array('0' =>$businessSA1,  '1' => $businessSA2);
			}

			if(isset($post['business-state'])){
				 $businessState = $post['business-state'];				
			}
			if(isset($post['business-location-phonenumber'])){
				$businessLocationPhonenumber = $post['business-location-phonenumber'];
			}
			if(isset($post['business-city'])){
				$businessCity = $post['business-city'];
			}
			if(isset($post['business-pincode'])){
				$businessPincode = $post['business-pincode'];
			}
			
			if(isset($post['business-website'])){
				$businessWebsite = $post['business-website'];
			}
			
			
			if(isset($post['business-location-name2'])){
				$businessLocationName2 = $post['business-location-name2'];
			}
			if(isset($post['business-street-address-12'])){
				$businessSA12 = $post['business-street-address-12'];
			}
			if(isset($post['business-street-address-22'])){
				$businessSA22 = $post['business-street-address-22'];
				
			}
			if($businessSA22!=''){
				$businessstreet2 = array('0' =>$businessSA12,  '1' => $businessSA22);	
			}else{
				$businessstreet2 = $businessSA12;
			}
			
			if(isset($post['business-state2'])){
				$businessState2 = $post['business-state2'];
			}
			if(isset($post['business-location-phonenumber2'])){
				$businessLocationPhonenumber2 = $post['business-location-phonenumber2'];
			}
			if(isset($post['business-city2'])){
				$businessCity2 = $post['business-city2'];
			}
			if(isset($post['business-pincode2'])){
				$businessPincode2 = $post['business-pincode2'];
			}
			if(isset($post['business-website2'])){
				$businessWebsite2 = $post['business-website2'];
			}
			
			
			if(isset($post['business-location-name3'])){
				$businessLocationName3 = $post['business-location-name3'];
			}
			if(isset($post['business-street-address-13'])){
				$businessSA13 = $post['business-street-address-13'];
			}
			if(isset($post['business-street-address-23'])){
				$businessSA23 = $post['business-street-address-23'];
			}
			if($businessSA23!=''){
				$businessstreet3 = array('0' =>$businessSA13,  '1' => $businessSA23);	
			}else{
				$businessstreet3 = $businessSA13;
			}
			if(isset($post['business-state3'])){
				$businessState3 = $post['business-state3'];
			}

			if(isset($post['business-location-phonenumber3'])){
				$businessLocationPhonenumber3 = $post['business-location-phonenumber3'];
			}
			if(isset($post['business-city3'])){
				$businessCity3 = $post['business-city3'];
			}
			if(isset($post['business-pincode3'])){
				$businessPincode3 = $post['business-pincode3'];
			}
			if(isset($post['business-website3'])){
				$businessWebsite3 = $post['business-website3'];
			}
			if(isset($post['password'])){
				 $password = $post['password']; 
			}
				
    	}
    	else{
    		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        	$resultRedirect->setUrl($this->_redirect->getRefererUrl());
        	return $resultRedirect; 
    	}
    	
        if ($customerId) {
            $customer = $this->CustomerRepositoryInterface->getById($customerId);
            $customer->setFirstname($firstname);
            $customer->setLastname($lastname);
            $customer->setEmail($businessEmail);
             $customer->setCustomAttribute('business_phone_number_checkout', $businessPhonenumber);
             $customer->setCustomAttribute('business_name', $businessName);
             $customer->setCustomAttribute('business_category', $businessCategory);

            //$customer->setDob($date);
            //$customer->setCustomAttribute('business-category', $businessCategory);
            //$customer->setGender($gender);
             $this->CustomerRepositoryInterface->save($customer);
            /*try {
            	$this->CustomerRepositoryInterface->save($customer);
           	} 	catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addException(
                    $e,
                    __('%1', $e->getMessage())
                );
                
                header("Location: http://www.freeboxes.com/profile/settings");
                exit();
            }*/

           $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $modelcollection = $objectManager->get('\Icecube\Businesses\Model\ResourceModel\Businesses\CollectionFactory')->create();
			$modelcollection->addFieldToFilter('businessuser_id' , $customerId);
			$businessData = $modelcollection->getFirstItem();
			
			if($businessData->getId()){
				$businessTimeline = $objectManager->create('Icecube\Businesses\Model\Index');
				$businessTimeline->load($businessData->getId(), 'business_id');
				$businessTimeline->setData('tag_line',$tagline);
				$businessTimeline->setData('main_website_url',$mainwebsiteurl);
				$businessTimeline->setData('company_description',$companyDescription);
				$businessTimeline->save();
			}

            $billingAddressId = $customer->getDefaultBilling();
                
            if($totaladdress1!="" && $totaladdress2 == "" && $totaladdress3 == ""){ 
            	//echo $businessState; die;
            	//echo $businessLocationName; die;
            	$address = $this->addressRepository->getById($totaladdress1);
	          	$address->setCustomAttribute('location_name', $businessLocationName);
	            $address->setTelephone($businessLocationPhonenumber);
	            $address->setPostcode($businessPincode);
	            $address->setCity($businessCity);
	            $address->setRegionId($businessState);
	           	$address->setCustomAttribute('website_name', $businessWebsite);
	           	$address->setStreet($businessstreet); 
	           	$address->setFirstname($firstname);
            	$address->setLastname($lastname);
	           	$address->setCountryId('US');  
	            $this->addressRepository->save($address);
	        }

	        if($totaladdress1!="" && $totaladdress2 != "" && $totaladdress3 == ""){ 
	        	//echo"I am here"; die;
	        	$address = $this->addressRepository->getById($totaladdress1);
	          	$address->setCustomAttribute('location_name', $businessLocationName);
	            $address->setTelephone($businessLocationPhonenumber);
	            $address->setPostcode($businessPincode);
	            $address->setCity($businessCity);
	            $address->setRegionId($businessState);
	           	$address->setCustomAttribute('website_name', $businessWebsite);
	           	$address->setStreet($businessstreet);
	           	$address->setFirstname($firstname);
            	$address->setLastname($lastname);
	           	$address->setCountryId('US');     
	            $this->addressRepository->save($address);
	        	
	        	if($totaladdress2=="test"){
	        		//echo $totaladdress2; die;
	        		//echo $businessLocationName2; die;
		            /* ------------------Second location start----------------------*/
		            $addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');
                    $address = $addresss->create();
                    $address->setCustomerId($customerId);
                    $address->setCustomAttribute('location_name', $businessLocationName2);
		            $address->setTelephone($businessLocationPhonenumber2);
		            $address->setPostcode($businessPincode2);
		            $address->setCity($businessCity2);
		            $address->setRegionId($businessState2);
		            $address->setFirstname($firstname);
            		$address->setLastname($lastname);
	           		$address->setCountryId('US');  
		           	$address->setCustomAttribute('website_name', $businessWebsite2);
		           	$address->setStreet($businessstreet2);
		           	$address->save();
		           	$address->getId(); 
		           	$addressnew = $this->addressRepository->getById($address->getId());
	          		$addressnew->setCustomAttribute('location_name', $businessLocationName2);
	          		$address->save();
                    /*try{
                            $address->save();
                    }
                    catch (Exception $e) {
                            Zend_Debug::dump($e->getMessage());
                    }	*/	            
		            /*---------------------Second location end----------------------*/	   	 
		        }else{
		        	
		        	$address = $this->addressRepository->getById($totaladdress2);
		        	$address->setCustomAttribute('location_name', $businessLocationName2);
		            $address->setTelephone($businessLocationPhonenumber2);
		            $address->setPostcode($businessPincode2);
		            $address->setCity($businessCity2);
		            $address->setRegionId($businessState2);
		            $address->setFirstname($firstname);
            		$address->setLastname($lastname);
	           		$address->setCountryId('US');  
		           	$address->setCustomAttribute('website_name', $businessWebsite2);
		           	$address->setStreet($businessstreet2);
		           	$this->addressRepository->save($address);
		        }
	        }
	        if($totaladdress3!="" && $totaladdress1!= "" && $totaladdress2!= ""){ 
	        	
	        	$address = $this->addressRepository->getById($totaladdress1);
	          	$address->setCustomAttribute('location_name', $businessLocationName);
	            $address->setTelephone($businessLocationPhonenumber);
	            $address->setPostcode($businessPincode);
	            $address->setCity($businessCity);
	            $address->setRegionId($businessState);
	            $address->setFirstname($firstname);
            	$address->setLastname($lastname);
	           	$address->setCountryId('US');  
	           	$address->setCustomAttribute('website_name', $businessWebsite);
	           	$address->setStreet($businessstreet);   
	            $this->addressRepository->save($address);
	        	
	        	if($totaladdress2=="test"){

		            /* ------------------Second location start----------------------*/
		            $addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');
                    $address = $addresss->create();
                    $address->setCustomerId($customerId);
                    $address->setCustomAttribute('location_name', $businessLocationName2);
		            $address->setTelephone($businessLocationPhonenumber2);
		            $address->setPostcode($businessPincode2);
		            $address->setCity($businessCity2);
		            $address->setRegionId($businessState2);
		            $address->setFirstname($firstname);
            		$address->setLastname($lastname);
	           		$address->setCountryId('US');  
		           	$address->setCustomAttribute('website_name', $businessWebsite2);
		           	$address->setStreet($businessstreet2);
		           	$address->save();
		           	$address->getId();
		           	$addressnew = $this->addressRepository->getById($address->getId());
	          		$addressnew->setCustomAttribute('location_name', $businessLocationName2);
	          		$address->save();
                    /*try{
                            $address->save();
                    }
                    catch (Exception $e) {
                            Zend_Debug::dump($e->getMessage());
                    }	*/	            
		            /*---------------------Second location end----------------------*/	   	 
		        }else{
		        	
		        	$address = $this->addressRepository->getById($totaladdress2);
		        	$address->setCustomAttribute('location_name', $businessLocationName2);
		            $address->setTelephone($businessLocationPhonenumber2);
		            $address->setPostcode($businessPincode2);
		            $address->setCity($businessCity2);
		            $address->setRegionId($businessState2);
		            $address->setFirstname($firstname);
            		$address->setLastname($lastname);
	           		$address->setCountryId('US');  
		           	$address->setCustomAttribute('website_name', $businessWebsite2);
		           	$address->setStreet($businessstreet2);
		           	$this->addressRepository->save($address);
		        }


		        if($totaladdress3=="test"){
		        	$addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');
                    $address = $addresss->create();
                    $address->setCustomerId($customerId);		    
		          	$address->setCustomAttribute('location_name', $businessLocationName3);
		            $address->setTelephone($businessLocationPhonenumber3);
		            $address->setPostcode($businessPincode3);
		            $address->setCity($businessCity3);
		            $address->setRegionId($businessState3);
		           	$address->setCustomAttribute('website_name', $businessWebsite3);
		           	$address->setStreet($businessstreet3);  
		           	$address->setFirstname($firstname);
            		$address->setLastname($lastname);
	           		$address->setCountryId('US'); 
	           		$address->save();
	           		$address->getId();
		           	$addressnew = $this->addressRepository->getById($address->getId());
	          		$addressnew->setCustomAttribute('location_name', $businessLocationName3); 
	          		$address->save(); 
		            /*try{
                            $address->save();
                    }
                    catch (Exception $e) {
                            Zend_Debug::dump($e->getMessage());
                    }	*/
	        	}else{

	        		$address = $this->addressRepository->getById($totaladdress3);
	        		$address->setCustomAttribute('location_name', $businessLocationName3);
		            $address->setTelephone($businessLocationPhonenumber3);
		            $address->setPostcode($businessPincode3);
		            $address->setCity($businessCity3);
		            $address->setRegionId($businessState3);
		           	$address->setCustomAttribute('website_name', $businessWebsite3);
		           	$address->setStreet($businessstreet3);  
		           	$address->setFirstname($firstname);
            		$address->setLastname($lastname);
	           		$address->setCountryId('US');   
		            $this->addressRepository->save($address);

	        	}
	        }
	        if($password){
                $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
                $customerSecure->setRpToken(null);
                $customerSecure->setRpTokenCreatedAt(null);
                $customerSecure->setPasswordHash($this->encryptor->getHash($password, true));
                $this->CustomerRepositoryInterface->save($customer);
            }

            $this->messageManager->addSuccess( __('Your profile settings have been successfully updated!') );
            
        	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        	$resultRedirect->setUrl($this->_redirect->getRefererUrl());

        	return $resultRedirect; 
        }else{
        	header('Location: '.$baseUrl.'login/');
        	exit();
        }

    }
}