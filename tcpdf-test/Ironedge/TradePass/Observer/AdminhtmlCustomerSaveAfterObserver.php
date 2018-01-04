<?php

namespace Ironedge\TradePass\Observer;

use Magento\Framework\Event\ObserverInterface;
use Ironedge\TradePass\Model\ResourceModel\TradePass\CollectionFactory;

class AdminhtmlCustomerSaveAfterObserver implements ObserverInterface {

    protected $_objectManager;
	protected $_transportBuilder;
    public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
    	\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->_objectManager = $objectManager;
        $this->_transportBuilder = $transportBuilder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) 
	{
        $customer = $observer->getCustomer();
        $customerid = $customer->getId();
        /*if($customerid==312){
        	$templateId =4;
        	$storeId =1;
			$transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
	            ->setTemplateOptions(['area' => Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
	            //->setTemplateVars($templateParams)
	            ->setFrom('chiragit0070@email.com')
	            ->addTo('chiragit007@email.com')
	            ->getTransport();
	        $transport->sendMessage();
		}*/
        $postData = $observer->getRequest()->getPostValue();
		//echo "<pre>"; print_r($postData);exit;
		//echo $postData['customer']['is_tradepass_confirmed']; die;
        if ($postData) {
            $tradePassCollection = $this->_objectManager->create(
                            'Ironedge\TradePass\Model\TradePass'
                    )->getCollection()
                    ->addFieldToFilter(
                    'customer_id', $customerid
            );
            $tradePassId = '';
            foreach ($tradePassCollection as $value){
				$tradePassId = $value->getId();
			}
                
			if($tradePassId!=''){
				$value = $this->_objectManager->create(
                            'Ironedge\TradePass\Model\TradePass'
                    )->load($tradePassId);
	            $value->addData($postData);
	            $value->setCustomerId($customerid);
	            $value->save();
				
				//start
				$tradePass = $this->_objectManager->create(
	                        'Ironedge\TradePass\Model\TradePass'
	                )->getCollection()
	                ->addFieldToFilter(
	                'customer_id', $customerid
	        		)->getFirstItem()->getData();   
				
				$confirmation = false;
				$valueNew = $this->_objectManager->create(
							'Ironedge\TradePass\Model\TradePass'
					)->load($tradePassId);
					
				if (isset($postData['customer']['is_tradepass_confirmed']) && $postData['customer']['is_tradepass_confirmed'])
				{
					$confirmation = true;
					$confirmationValue = 'confirmed';
				}else{
					$confirmationValue = 'not_confirmed';
					$valueNew->setIsConfirmed($confirmationValue);
					$valueNew->save();
				}
				if($confirmation){
					$valueNew->setIsConfirmed($confirmationValue);
					$valueNew->save();
					if($tradePass['is_confirmed_mail']==0)
					{
						$email = $customer->getEmail();
						$emailTempVariables = array();
						$senderName = 'Customer Support';
						$senderEmail = 'info@ironedge.com.au';
						$postObject = new \Magento\Framework\DataObject();
						$postObject->setData($emailTempVariables);
			
						$sender = [
									'name' => $senderName,
									'email' => $senderEmail,
								  ];
			
						$transport = $this->_transportBuilder->setTemplateIdentifier('tradepass_email_template')
							->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
							->setTemplateVars(['data' => $postObject])
							->setFrom($sender)
							->addTo($email)
							->setReplyTo($senderEmail)            
							->getTransport();               
						$transport->sendMessage();
						
						//$postDataNew['is_confirmed_mail'] = '1';
						$valueNew->setIsConfirmedMail(1);
						$valueNew->save();
					}
					
				}
			}
            
	    }
    }

}
