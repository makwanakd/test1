<?php

namespace Ironedge\TradePass\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class CheckLoginConfirmed implements ObserverInterface {

    protected $_objectManager;
    protected $_messageManager;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_url;
    protected $_responseFactory;
    protected $redirect;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
            \Magento\Framework\Message\ManagerInterface $messageManager, 
            \Magento\Framework\App\ResponseFactory $responseFactory, 
            \Magento\Framework\UrlInterface $url
            ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
		
        $customerId = $observer->getCustomer()->getId();
         $tradePass = $this->_objectManager->create(
                            'Ironedge\TradePass\Model\TradePass'
                    )->getCollection()
                    ->addFieldToFilter(
                    'customer_id', $customerId
            )->getFirstItem()->getData();   
			var_dump($tradePass);

        if (isset($tradePass['is_confirmed']) && $tradePass['is_confirmed']=='not_confirmed' ){
            $message = __('This account is not confirmed.');
            $this->_messageManager->addError($message);
            $this->_responseFactory->create()->setRedirect($this->_url->getUrl('customer/account/login'))->sendResponse();
            /* die use for stop excaution */
            exit();
        }
        return $this;
    }

}
