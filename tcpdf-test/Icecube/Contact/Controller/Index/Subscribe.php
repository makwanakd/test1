<?php

namespace Icecube\Contact\Controller\Index;
use Magento\Newsletter\Model\SubscriberFactory;

class Subscribe extends \Magento\Contact\Controller\Index
{
	protected $_helper;
    
    protected $_api = null;
    
	protected $resultJsonFactory;
	
	protected $_subscriberFactory;
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Ebizmarts\MailChimp\Helper\Data $helper,
        SubscriberFactory $subscriberFactory
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
        
    }
	public function execute()
    {
		$post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect('*/*/');
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
			$this->_subscriberFactory->create()->subscribe($post['email']);
					//$email = 'test8.capital@gmail.com';
					$email = $post['email'];
					$telephone = $post['phonenumber'];
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
		            $mergeVars = array("FNAME" => $post['firstname'], "LNAME" => $post['lastname'], "TELEPHONE" => $finalNumber, "CUSTBUSI" => $post['cust-or-business'], "BUSINESS" => $post['businessname']);
		            //$mergeVars = array("FNAME" => "test", "LNAME" => "test2", "GENDER" => "Female", "DOB" => "01/01", "BTELEPHONE" => "7798798", "STELEPHONE" => "7798798", "CGROUP" => "General", "STOREID" => "1");
		            $api = $this->_api;
		            //$api = $this->_api;
		            
		            $status = 'pending';
		            try {
		                $emailHash = md5(strtolower($email));
		            	$return = $api->lists->members->addOrUpdate('86d7f645c4', $emailHash, null, $status, $mergeVars, null, null, null, null, $email, $status);
		            } catch (\Exception $e) {
		                $this->_helper->log($e->getMessage());
		                $data['type'] = 'error';
			            $data['message'] = __('There was a problem with the subscription: %1', $e->getMessage());
			            $result = $this->resultJsonFactory->create();
						return $result->setData($data);
		            }
		        
            //mailchimp integration ends
          
            $data['type'] = 'success';
            $data['message'] = 'Thank you for reaching out to freeboxes. One of our specialists will contact you as soon as possible. Have a FREEkin awesome day!';
            $result = $this->resultJsonFactory->create();
			return $result->setData($data);	
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $data['type'] = 'error';
            $data['message'] = __('There was a problem with the subscription: %1', $e->getMessage());
            $result = $this->resultJsonFactory->create();
			return $result->setData($data);	
        }
	}
}