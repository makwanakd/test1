<?php

namespace Icecube\Contact\Controller\Index;

use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;

class Pennsylvaniasubmit extends \Magento\Newsletter\Controller\Subscriber\NewAction
{
	protected $customerAccountManagement;
	
	protected $_helper;
    
    protected $_api = null;
    
	public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $customerAccountManagement,
        \Ebizmarts\MailChimp\Helper\Data $helper
    ) {
        $this->customerAccountManagement = $customerAccountManagement;
        \Magento\Newsletter\Controller\Subscriber::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl
        );
        $this->_helper          = $helper;
        $this->_api             = $this->_helper->getApi();
    }
	public function execute()
    {
		$post = $this->getRequest()->getPostValue();
        if (!$post) {
            //$this->_redirect('business-signup');
            $this->_redirect('');
            return;
        }
            //mailchimp integration starts
            if(isset($post['pnpopupemail'])){
            		//$email = 'test8.capital@gmail.com';
					$email = $post['pnpopupemail'];
					
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$region = $objectManager->create('Magento\Directory\Model\Region')->load($post['region_id_pennsylvania']);
					$regionName = '';
                    if ($region) {
                        $regionName = $region->getName();
                    }
                    $telephone = $post['pnpopupphone'];
					$finalNumber = '';
					if($telephone!=NULL && $telephone!=''){
						$telephone = str_replace(' ','',$telephone);
						$telephone = str_replace('-','',$telephone);
						$telephone = str_replace('(','',$telephone);
						$telephone = str_replace(')','',$telephone);
						
						if(strlen($telephone)==10){
							$finalNumber = substr($telephone,0,3);
							$finalNumber .= '-';
							$finalNumber .= substr($telephone,3,3);
							$finalNumber .= '-';
							$finalNumber .= substr($telephone,6,4);
						}
					}		            
		            $mergeVars = array("FNAME" => $post['pnpopupfname'], "LNAME" => $post['pnpopuplname'], "STATE" => $regionName, "PHONE" => $finalNumber, "CITY" => $post['pnpopupcity'], "ZIP" => $post['pnpopupzip']);
		            //$mergeVars = array("FNAME" => "test", "LNAME" => "test2", "GENDER" => "Female", "DOB" => "01/01", "BTELEPHONE" => "7798798", "STELEPHONE" => "7798798", "CGROUP" => "General", "STOREID" => "1");
		            $api = $this->_api;
		            //$api = $this->_api;
		            
		            $status = 'pending';
		            try {
		                $emailHash = md5(strtolower($email));
		            	$return = $api->lists->members->addOrUpdate('c0d8dbcbd2', $emailHash, null, $status, $mergeVars, null, null, null, null, $email, $status);
		            	$this->messageManager->addSuccess(__('Thank you for your subscription. Please check your email to complete your signup.'));
		            } catch (\Exception $e) {
		                $this->_helper->log($e->getMessage());
		                $this->messageManager->addException(
		                    $e,
		                    __('There was a problem with the subscription: %1', $e->getMessage())
		                );
		            }
		    }
            //mailchimp integration ends

       //$this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
       $this->getResponse()->setRedirect('/');
	}
}