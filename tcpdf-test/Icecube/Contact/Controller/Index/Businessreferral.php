<?php

namespace Icecube\Contact\Controller\Index;
use Magento\Newsletter\Model\SubscriberFactory;

class Businessreferral extends \Magento\Contact\Controller\Index
{
	protected $_helper;
    
    protected $_api = null;
    
	protected $resultJsonFactory;
	
	protected $_subscriberFactory;
	
	protected $userFactory;
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Ebizmarts\MailChimp\Helper\Data $helper,
        SubscriberFactory $subscriberFactory,
        \Magento\User\Model\UserFactory $userFactory
    ) {
        parent::__construct($context,$transportBuilder,$inlineTranslation,$scopeConfig,$storeManager);
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        
        $this->_helper          = $helper;
        $this->_api             = $this->_helper->getApi();
        $this->_subscriberFactory = $subscriberFactory;
        
        $this->userFactory = $userFactory;
        
    }
	public function execute()
    {
		$post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect('business-referral');
            return;
        }
        $this->inlineTranslation->suspend();
        try {
            //mailchimp integration starts
            /*if(isset($post['is_subscribed'])){
            	if($post['is_subscribed']==1){
					$this->_subscriberFactory->create()->subscribe($post['email']);
				}
			}*/
			//$this->_subscriberFactory->create()->subscribe($post['email']);
					//$email = 'test8.capital@gmail.com';
					$email = $post['email'];
					$telephone = $post['mmerge4'];
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
		            
		            //$mergeVars = array("FNAME" => "test", "LNAME" => "test2", "GENDER" => "Female", "DOB" => "01/01", "BTELEPHONE" => "7798798", "STELEPHONE" => "7798798", "CGROUP" => "General", "STOREID" => "1");
		            $api = $this->_api;
		            //$api = $this->_api;
		            
		            //$status = 'pending';
		            $status = 'subscribed';
		            try {
		                $emailHash = md5(strtolower($email));
		                
		                if(isset($post['is_subscribed'])){
			            	if($post['is_subscribed']==1){
			            		//$mergeVars = array("FNAME" => $post['fname'], "LNAME" => $post['lname'], "TELEPHONE" => $finalNumber, "CUSTBUSI" => $post['cust-or-business'], "BUSINESS" => $post['businessname']);
			            		
			            		$mergeVars = array("FNAME" => $post['fname'], "LNAME" => $post['lname'], "TELEPHONE" => $finalNumber);
								$return = $api->lists->members->addOrUpdate('86d7f645c4', $emailHash, null, $status, $mergeVars, null, null, null, null, $email, $status);
								
								$this->_subscriberFactory->create()->subscribe($post['email']);
							}
						}
						
						/*business telephone*/
						$telephone = $post['mmerge8'];
						$bfinalNumber = '';
						if($telephone!=NULL && $telephone!=''){
							$telephone = str_replace(' ','',$telephone);
							$telephone = str_replace('-','',$telephone);
							$telephone = str_replace('(','',$telephone);
							$telephone = str_replace(')','',$telephone);
							
							if(strlen($telephone)==10){
								$bfinalNumber = substr($telephone,0,3);
								$bfinalNumber .= '-';
								$bfinalNumber .= substr($telephone,3,3);
								$bfinalNumber .= '-';
								$bfinalNumber .= substr($telephone,6,4);
							}
						}
						
						$mergeVars = array("FNAME" => $post['fname'], "LNAME" => $post['lname'], "MMERGE4" => $finalNumber, "MMERGE3" => $post['mmerge3'], "MMERGE9" => $post['mmerge9'], "MMERGE5" => $post['mmerge5'], "MMERGE6" => $post['mmerge6'], "MMERGE7" => $post['mmerge7'], "MMERGE8" => $bfinalNumber, "MMERGE10" => $post['mmerge10']);
						$return = $api->lists->members->addOrUpdate('30799b3538', $emailHash, null, $status, $mergeVars, null, null, null, null, $email, $status);
						
						/*Admin notification mail starts*/
						
						$this->notifyAdmin(
				            $email,$post['fname'],$post['lname'],
				            ["email" => $post['email'], "fname" => $post['fname'], "lname" => $post['lname'], "mmerge4" => $finalNumber, "mmerge3" => $post['mmerge3'], "mmerge9" => $post['mmerge9'], "mmerge5" => $post['mmerge5'], "mmerge6" => $post['mmerge6'], "mmerge7" => $post['mmerge7'], "mmerge8" => $bfinalNumber, "mmerge10" => $post['mmerge10']],
				            0
				        );
        
						/*Admin notification mail ends*/
						
						
		            } catch (\Exception $e) {
		                $this->_helper->log($e->getMessage());
		                $data['type'] = 'error';
			            $data['message'] = __('There was a problem with the subscription: %1', $e->getMessage());
			            $result = $this->resultJsonFactory->create();
						return $result->setData($data);	
		            }
		        
            //mailchimp integration ends
          
            $data['type'] = 'success';
            $data['message'] = 'Thank you for your submission! We will contact the Business you recommended. Once your recommended Business sign ups for our Premium Package, we will reward you with a $200 Visa gift card!';
            $result = $this->resultJsonFactory->create();
			return $result->setData($data);	
        } catch (\Exception $e) {
            $data['type'] = 'error';
            $data['message'] = __('There was a problem with the subscription: %1', $e->getMessage());
            $result = $this->resultJsonFactory->create();
			return $result->setData($data);	
        }
	}
	private function notifyAdmin(
        $email,$fname,$lname,
        $templateParams = [],
        $storeId = null
    ) {
        $templateId = 6; 
        
		$from = array('email' => $email, 'name' => $fname.' '.$lname);
		//$to = array('aniruddh@icecubedigital.com');
		//$to = array('info@freeboxes.com');
		//$to = array('magento.icecube@gmail.com');
		$adminEmails = array();
		$adminUsers = $this->userFactory->create()->getCollection();
    	//$adminUsers->load();
    	foreach($adminUsers as $admin){
    		$roleName = $admin->getRole()->getRoleName();
    		if($roleName=="Administrators"){
				//$admin->getEmail();
				$adminEmails[] = array($admin->getEmail());
			}
    		
			
		}
		foreach($adminEmails as $adminEmail){
			$transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
	            ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
	            ->setTemplateVars($templateParams)
	            ->setFrom($from)
	            ->addTo($adminEmail)
	            ->getTransport();
	        $transport->sendMessage();
		}
        
    }
}