<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ironedge\TradePass\Controller\Account;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Helper\Address;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\Registration;
use Magento\Framework\Escaper;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;


ini_set('display_errors',1);
error_reporting(E_ALL);
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreatePost extends \Magento\Customer\Controller\AbstractAccount {

    /** @var AccountManagementInterface */
    protected $accountManagement;

    /** @var Address */
    protected $addressHelper;

    /** @var FormFactory */
    protected $formFactory;

    /** @var SubscriberFactory */
    protected $subscriberFactory;

    /** @var RegionInterfaceFactory */
    protected $regionDataFactory;

    /** @var AddressInterfaceFactory */
    protected $addressDataFactory;

    /** @var Registration */
    protected $registration;

    /** @var CustomerInterfaceFactory */
    protected $customerDataFactory;

    /** @var CustomerUrl */
    protected $customerUrl;

    /** @var Escaper */
    protected $escaper;

    /** @var CustomerExtractor */
    protected $customerExtractor;

    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;

    /** @var DataObjectHelper  */
    protected $dataObjectHelper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var AccountRedirect
     */
    private $accountRedirect;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
	
    /**
     * @var TradePass
     */	 
    protected $tradePass;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $fileSystem;
    protected $uploaderFactory;
    protected $allowedExtensions = ['csv','pdf','jpg','gif','xls','docx'];
    protected $fileId = 'file';
	 
    protected $addressFactory;
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $accountManagement
     * @param Address $addressHelper
     * @param UrlFactory $urlFactory
     * @param FormFactory $formFactory
     * @param SubscriberFactory $subscriberFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param AddressInterfaceFactory $addressDataFactory
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param CustomerUrl $customerUrl
     * @param Registration $registration
     * @param Escaper $escaper
     * @param CustomerExtractor $customerExtractor
     * @param DataObjectHelper $dataObjectHelper
     * @param AccountRedirect $accountRedirect
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    Context $context, Session $customerSession, ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager, AccountManagementInterface $accountManagement, Address $addressHelper, UrlFactory $urlFactory, FormFactory $formFactory, SubscriberFactory $subscriberFactory, RegionInterfaceFactory $regionDataFactory, AddressInterfaceFactory $addressDataFactory, CustomerInterfaceFactory $customerDataFactory, CustomerUrl $customerUrl, Registration $registration, Escaper $escaper, CustomerExtractor $customerExtractor, DataObjectHelper $dataObjectHelper, AccountRedirect $accountRedirect, 
            \Magento\Customer\Model\CustomerFactory $customerFactory,
            \Magento\Customer\Model\AddressFactory $addressFactory,
			\Ironedge\TradePass\Model\TradePass $tradePass 
			, Filesystem $fileSystem, UploaderFactory $uploaderFactory
    ) {
        $this->tradePass = $tradePass;		
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;		
        $this->addressFactory = $addressFactory;        
        $this->customerFactory = $customerFactory;
        $this->session = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->accountManagement = $accountManagement;
        $this->addressHelper = $addressHelper;
        $this->formFactory = $formFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->regionDataFactory = $regionDataFactory;
        $this->addressDataFactory = $addressDataFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerUrl = $customerUrl;
        $this->registration = $registration;
        $this->escaper = $escaper;
        $this->customerExtractor = $customerExtractor;
        $this->urlModel = $urlFactory->create();
        $this->dataObjectHelper = $dataObjectHelper;
        $this->accountRedirect = $accountRedirect;
        parent::__construct($context);
    }

    /**
     * Retrieve cookie manager
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager() {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                    \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory() {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                    \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }


    /**
     * Create customer account action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute() {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        if (!$this->getRequest()->isPost()) {
            $url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
            $resultRedirect->setUrl($this->_redirect->error($url));
            return $resultRedirect;
        }

        $this->session->regenerateId();

        try {


            $password = $this->getRequest()->getParam('password');
            $firstname = $this->getRequest()->getParam('firstname');
            $lastname = $this->getRequest()->getParam('lastname');

            $email = $this->getRequest()->getParam('email');
            $gender = $this->getRequest()->getParam('gender');
            $dob = $this->getRequest()->getParam('dob');
            $confirmation = $this->getRequest()->getParam('password_confirmation');
            $redirectUrl = $this->session->getBeforeAuthUrl();

            $this->checkPasswordConfirmation($password, $confirmation);

            $websiteId = $this->storeManager->getWebsite()->getWebsiteId();

            // Instantiate object (this is the most important part)
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);

            // Preparing data for new customer
            $customer->setEmail($email);
            $customer->setFirstname($firstname);
            $customer->setLastname($lastname);
            $customer->setPassword($password);
            $customer->setDob($dob);
            $customer->setGender($gender);
            $customer->setConfirmation($customer->getRandomConfirmationKey());			
            //$customer->setAddresses($addresses);
            $customer->save();

             $address = $this->addressFactory->create()->setCustomerId($customer->getId())
                    ->setFirstname($customer->getFirstname())
                    ->setMiddleName($customer->getMiddlename())
                    ->setLastname($customer->getLastname())
                    ->setCountryId($this->getRequest()->getParam('country_id'))
                    ->setRegionId($this->getRequest()->getParam('region_id')) //state/province, only needed if the country is USA
                    ->setPostcode($this->getRequest()->getParam('postcode'))
                    ->setCity($this->getRequest()->getParam('city'))
                    ->setTelephone($this->getRequest()->getParam('telephone'))
                    ->setCompany($this->getRequest()->getParam('company'))
                    ->setStreet($this->getRequest()->getParam('street'))
                    ->setIsDefaultBilling('1')
                    ->setIsDefaultShipping('1')
                    ->setSaveInAddressBook('1');
            $address->save();

			
            $destinationPath = $this->getDestinationPath();

            try {
                $uploader = $this->uploaderFactory->create(['fileId' => $this->fileId])
                        ->setAllowCreateFolders(true)
                        ->setAllowedExtensions($this->allowedExtensions)
                        ->addValidateCallback('validate', $this, 'validateFile');
                $result = $uploader->save($destinationPath);                
                if (!$result) {
                    throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                    );
                }

                // @todo
                // process the uploaded file
            } catch (\Exception $e) {
                throw new InputException(__('Please make sure your passwords match.'));
            }
			

            $postData['industry'] = $this->getRequest()->getParam('industry');
            $postData['abn'] = $this->getRequest()->getParam('abn');
            $postData['referrer'] = $this->getRequest()->getParam('referrer');
            $postData['file'] = $result['file'];
            $value = $this->tradePass->load(0);
            $value->addData($postData);
            $value->setCustomerId($customer->getId());
            $value->save();




            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
            }

            $this->_eventManager->dispatch(
                    'customer_register_success', ['account_controller' => $this, 'customer' => $customer]
            );

            $confirmationStatus = 'account_confirmation_required';//$this->accountManagement->getConfirmationStatus($customer->getId());
            if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                //$email = $this->customerUrl->getEmailConfirmationUrl($customer->getEmail());
                // @codingStandardsIgnoreStart
                $this->messageManager->addSuccess(__('Please allow up to 48 hours for your account to be approved.'));
                // @codingStandardsIgnoreEnd
                $url = $this->urlModel->getUrl('tradepass/account/create', ['_secure' => true]);
                $resultRedirect->setUrl($this->_redirect->success($url));
            } else {
                $this->session->setCustomerDataAsLoggedIn($customer);
                $this->messageManager->addSuccess($this->getSuccessMessage());
                $requestedRedirect = $this->accountRedirect->getRedirectCookie();
                if (!$this->scopeConfig->getValue('customer/startup/redirect_dashboard') && $requestedRedirect) {
                    $resultRedirect->setUrl($this->_redirect->success($requestedRedirect));
                    $this->accountRedirect->clearRedirectCookie();
                    return $resultRedirect;
                }
                $resultRedirect = $this->accountRedirect->getRedirect();
            }
            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }

            return $resultRedirect;
        } catch (StateException $e) {
            $url = $this->urlModel->getUrl('customer/account/forgotpassword');
            // @codingStandardsIgnoreStart
            $message = __(
                    'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.', $url
            );
            // @codingStandardsIgnoreEnd
            $this->messageManager->addError($message);
        } catch (InputException $e) {
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addError($this->escaper->escapeHtml($error->getMessage()));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t save the customer.'.$e->getMessage()));
        }

        $this->session->setCustomerFormData($this->getRequest()->getPostValue());
        $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
        $resultRedirect->setUrl($this->_redirect->error($defaultUrl));
        return $resultRedirect;
    }

    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password
     * @param string $confirmation
     * @return void
     * @throws InputException
     */
    protected function checkPasswordConfirmation($password, $confirmation) {
        if ($password != $confirmation) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }

    /**
     * Retrieve success message
     *
     * @return string
     */
    protected function getSuccessMessage() {
        if ($this->addressHelper->isVatValidationEnabled()) {
            if ($this->addressHelper->getTaxCalculationAddressType() == Address::TYPE_SHIPPING) {
                // @codingStandardsIgnoreStart
                $message = __(
                        'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your shipping address for proper VAT calculation.', $this->urlModel->getUrl('customer/address/edit')
                );
                // @codingStandardsIgnoreEnd
            } else {
                // @codingStandardsIgnoreStart
                $message = __(
                        'If you are a registered VAT customer, please <a href="%1">click here</a> to enter your billing address for proper VAT calculation.', $this->urlModel->getUrl('customer/address/edit')
                );
                // @codingStandardsIgnoreEnd
            }
        } else {
            $message = __('Thank you for registering with %1.', $this->storeManager->getStore()->getFrontendName());
        }
        return $message;
    }
	
	public function getDestinationPath() {
        return $this->fileSystem
                        ->getDirectoryWrite(DirectoryList::MEDIA)
                        ->getAbsolutePath('/trade-pass/');
    }

    public function validateFile($filePath) {
        // @todo
        // your custom validation code here
    }	

}
