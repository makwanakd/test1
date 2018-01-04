<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ironedge\TradePass\Controller\Adminhtml\Doc;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class Moreinfo extends \Magento\Backend\App\Action {

    protected $fileFactory;
    protected $_transportBuilder;
	protected $_objectManager;

    public function __construct(
    \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, Filesystem $fileSystem, \Magento\Backend\App\Action\Context $context, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->fileFactory = $fileFactory;

        $this->fileSystem = $fileSystem;
		$this->_transportBuilder = $transportBuilder;
		parent::__construct($context);
    }

    public function execute() {
		
        //do your custom stuff here
        $customerId = $this->getRequest()->getParam('customer_id');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory'); 
    	$customer=$customerFactory->create();
    	$customer->setWebsiteId(1);
    	$customer->load($customerId);
		if($customer->getEmail()){
		
				$tradePassCollection = $this->_objectManager->create(
                            'Ironedge\TradePass\Model\TradePass'
	                    )->getCollection()
	                    ->addFieldToFilter(
	                    'customer_id', $customer->getId()
	            );
				$tradePassId = '';
	            foreach ($tradePassCollection as $value){
					$tradePassId = $value->getId();
				}
				if($tradePassId!=''){
					$value = $this->_objectManager->create(
	                            'Ironedge\TradePass\Model\TradePass'
	                    )->load($tradePassId);
		            $value->setMoreinfo(1);
					$value->save();
				}
	            
				
				
				$toemail = $customer->getEmail();
				$email = 'info@ironedge.com.au';
				$emailTempVariables = array('cid'=>$customer->getId(),'cemail'=>$email,'cname'=>$customer->getFirstname().' '.$customer->getLastname());
				$senderName = 'Iron Edge';
				$senderEmail = $email;
				$postObject = new \Magento\Framework\DataObject();
				$postObject->setData($emailTempVariables);
	
				$sender = [
							'name' => $senderName,
							'email' => $senderEmail,
						  ];
	
				$transport = $this->_transportBuilder->setTemplateIdentifier('tradepass_email_template_moreinfo')
					->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
					->setTemplateVars(['data' => $postObject])
					->setFrom($sender)
					->addTo($toemail)
					->setReplyTo($senderEmail)            
					->getTransport();               
				$transport->sendMessage();
			}
		
		$resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath(
                    'customer/index/edit',
                    ['id' => $customerId, '_current' => true]
                );
		$this->messageManager->addSuccess(__('More information request has been sent!'));
		return $resultRedirect;

    }

}
