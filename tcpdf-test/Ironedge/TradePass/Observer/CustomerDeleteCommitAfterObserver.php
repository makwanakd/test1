<?php

namespace Ironedge\TradePass\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerDeleteCommitAfterObserver implements ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {
        try {
            $customer = $observer->getCustomer();
            $customerid = $customer->getId();
            $tradepassCollection = $this->_objectManager->create(
                            'Ironedge\TradePass\Model\TradePass'
                    )
                    ->getCollection()
                    ->addFieldToFilter(
                    'customer_id', $customerid
            );
            foreach ($tradepassCollection as $customer)
                $customer->delete();
        } catch (\Exception $e) {
//            $this->messageManager->addError($e->getMessage());
        }
    }

}
