<?php
namespace Icecube\Settings\Controller\Account;
use Magento\Framework\Controller\ResultFactory;
class EditCustomer extends \Magento\Customer\Controller\AbstractAccount
{
    protected $customerRepositoryInterface;
    protected $customerSession;
    protected $request;
    protected $addressRepository;
    protected $encryptor;
    protected $customerRegistry;
  /*  protected $resultRedirect;*/

       public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\Session $customerSession,
        /*\Magento\Framework\App\Request\Http $request*/
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
        /*\Magento\Framework\Controller\ResultFactory $resultRedirect*/
      ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->CustomerRepositoryInterface = $customerRepositoryInterface;
        $this->Session = $customerSession;
        $this->addressRepository = $addressRepository;
        $this->encryptor = $encryptor;
        $this->customerRegistry = $customerRegistry;
        /*$this->resultFactory = $resultRedirect;*/
         //$this->request = $request;
      }
    public function execute()
    {
      $om = \Magento\Framework\App\ObjectManager::getInstance(); 
      $baseUrl = $om->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB); 
       $street = array();
        //echo "one";
        //print_r($_POST);die;
        $customerId = $this->Session->getCustomer()->getId();
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest()->getParams();  
        if($request){  
        if(isset($request['firstname'])){   
        }
        if(isset($request['firstname'])){  
            $firstname = $request['firstname'];  
          } 
          if(isset($request['lastname'])){  
            $lastname = $request['lastname'];  
          }
          if(isset($request['email'])){  
            $email = $request['email'];  
          }    
          if(isset($request['telephone'])){  
            $telephone = $request['telephone'];  
          } 
          if(isset($request['dobmonth'])){  
            $dobmonth = $request['dobmonth'];  
          } 
          if(isset($request['dobday'])){  
            $dobday = $request['dobday'];  
          }   
          if(isset($request['dobyear'])){  
            $dobyear = $request['dobyear'];  
          }
          if(isset($request['business_survey'])){  
            $business_survey = $request['business_survey'];  
          }
          if(isset($request['street1'])){  
            $street1 = $request['street1'];  
          }
          if(isset($request['street2'])){  
            $street2 = $request['street2'];  
          } 
          if(isset($request['gender'])){  
            $gender = $request['gender'];  
          } 
          if(isset($request['pnpopupcity'])){  
            $pnpopupcity = $request['pnpopupcity'];  
          } 
          if(isset($request['postcode'])){  
            $postcode = $request['postcode'];  
          } 
          if(isset($request['region_id'])){  
            $region_id = $request['region_id'];  
          } 
          if(isset($request['password'])){  
            $password = $request['password'];  
          }     
           $date = ($dobmonth.'/'.$dobday.'/'.$dobyear);              
            $street = array('0' =>$street1,  '1' => $street2);
            
        }else{
          $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
          $resultRedirect->setUrl($this->_redirect->getRefererUrl());
          return $resultRedirect;
        }
      
        $this->resultPageFactory->create();
        
        if ($customerId) {
             $customer = $this->CustomerRepositoryInterface->getById($customerId);
            $customer->setFirstname($firstname);
            $customer->setLastname($lastname);
            $customer->setEmail($email);
            $customer->setDob($date);
            $customer->setCustomAttribute('business_survey', $business_survey);
            $customer->setGender($gender);
            try {
            $this->CustomerRepositoryInterface->save($customer);
           } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addException(
                    $e,
                    __('%1', $e->getMessage())
                );
                
                header('Location: '.$baseUrl.'customers/settings');
                exit();

            } 


            $billingAddressId = $customer->getDefaultBilling();
             $billingAddressId; 

             $address = $this->addressRepository->getById($billingAddressId);
             $address->setTelephone($telephone);
             $address->setPostcode($postcode);
             $address->setCity($pnpopupcity);
            $address->setRegionId($region_id);
            $address->setFirstname($firstname);
            $address->setLastname($lastname);
             $address->setStreet($street);   

            $this->addressRepository->save($address);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $preferences = $request['hdnpreferences'];
            //echo $preferences; die;
            if($preferences){
            $preferences = explode(',',$preferences);
             //echo"<pre>";print_r($preferences); die; 
            }
            $preference1 = 0;
            $preference2 = 0;
            $preference3 = 0;
            if(!empty($preferences)){
                //echo"i ame here   fd"; die;
                if(isset($preferences[0])){

                    if($preferences[0]!='' && $preferences[0]!=NULL){
                        $preference1 = $preferences[0]; 
                    }else{ $preference1 = 0;  }
                }
                if(isset($preferences[1])){
                    if($preferences[1]!='' && $preferences[1]!=NULL){
                        $preference2 = $preferences[1];
                    }else{ $preference2 = 0; }
                }
                if(isset($preferences[2])){
                    if($preferences[2]!='' && $preferences[2]!=NULL){
                        $preference3 = $preferences[2];
                    }else{ $preference3 = 0; }
                }

                $sqlPreferences = "update mgsh_customer_preferences SET preference1 = '".$preference1."', preference2 = '".$preference2."', preference3 = '".$preference3."' WHERE customer_id = '".$customerId."'";
                $connection->query($sqlPreferences);
            }else{
                $preference1 = 0;
                $preference2 = 0;
                $preference3 = 0;

                $sqlPreferences = "update mgsh_customer_preferences SET preference1 = '".$preference1."', preference2 = '".$preference2."', preference3 = '".$preference3."' WHERE customer_id = '".$customerId."'";
                $connection->query($sqlPreferences);
            }
            if($password){
                $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
                $customerSecure->setRpToken(null);
                $customerSecure->setRpTokenCreatedAt(null);
                $customerSecure->setPasswordHash($this->encryptor->getHash($password, true));
                $this->CustomerRepositoryInterface->save($customer);
            }
            $this->messageManager->addSuccess( __('Your profile settings have been successfully updated!') );
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect; 
            /*header("Location: http://www.freeboxes.com/customers/settings/");
            exit();*/
        }else{
          header('Location: '.$baseUrl.'login/');
          exit();
        }
        
    }
}