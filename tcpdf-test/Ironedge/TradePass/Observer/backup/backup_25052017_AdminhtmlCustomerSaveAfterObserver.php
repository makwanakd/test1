<?php

namespace Ironedge\TradePass\Observer;

use Magento\Framework\Event\ObserverInterface;
use Ironedge\TradePass\Model\ResourceModel\TradePass\CollectionFactory;

class AdminhtmlCustomerSaveAfterObserver implements ObserverInterface {

    protected $_objectManager;
	protected $_transportBuilder;
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
    \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->_objectManager = $objectManager;
        $this->_transportBuilder = $transportBuilder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
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
//        print_r($postData);exit;
        if ($postData) {
            $tradePassCollection = $this->_objectManager->create(
                            'Ironedge\TradePass\Model\TradePass'
                    )->getCollection()
                    ->addFieldToFilter(
                    'customer_id', $customerid
            );
            $tradePassId = '';
            foreach ($tradePassCollection as $value)
                $tradePassId = $value->getId();

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
		if (isset($tradePass['is_confirmed']) && $tradePass['is_confirmed']=='confirmed' ){
			if($tradePass['is_confirmed_mail']==0){
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
	
	            $transport = $this->_transportBuilder->setTemplateIdentifier(5)
	            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
	            ->setTemplateVars(['data' => $postObject])
	            ->setFrom($sender)
	            ->addTo($email)
	            ->setReplyTo($senderEmail)            
	            ->getTransport();               
	            $transport->sendMessage();
				
				
				/*$postDataNew = array();
				$postDataNew['is_confirmed_mail'] = '1';*/
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$valueNew = $objectManager->create(
                            'Ironedge\TradePass\Model\TradePass'
                    )->load($tradePassId);
	            $valueNew->setIsConfirmedMail(1);
				//$valueNew->addData($postDataNew);
	            $valueNew->save();
				}
			}
			//ends
			
        }
    }

}
