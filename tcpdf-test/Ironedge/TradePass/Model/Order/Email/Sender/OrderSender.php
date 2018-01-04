<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ironedge\TradePass\Model\Order\Email\Sender;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
	function checkAndSend(\Magento\Sales\Model\Order $order)
    {
        $this->identityContainer->setStore($order->getStore());
        if (!$this->identityContainer->isEnabled()) {
            return false;
        }
        $this->prepareTemplate($order);

        /** @var SenderBuilder $sender */
        $sender = $this->getSender();

        try {
			if($order->getState() == 'complete') {
            	$sender->send(FALSE);
			}else{
				$sender->send();				
			}
            $sender->sendCopyTo();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return true;
    }
}
