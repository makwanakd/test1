<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ironedge\TradePass\Controller\Adminhtml\Order\Create;

class Reorder extends \Magento\Sales\Controller\Adminhtml\Order\Create\Reorder
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this->_getSession()->clearStorage();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        if (!$this->_objectManager->get('Magento\Sales\Helper\Reorder')->canReorder($order->getEntityId())) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($order->getId()) {
            $order->setReordered(true);
            $this->_getSession()->setUseOldShippingMethod(true);
             try{
					$this->_getOrderCreateModel()->initFromOrder($order);
					$resultRedirect->setPath('sales/*');
				}
				catch (\Magento\Framework\Exception\LocalizedException $e) {
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$messageManager = $objectManager->get('Magento\Framework\Message\ManagerInterface');
					$messageManager->addError(__($e->getMessage()));
					$resultRedirect->setPath('sales/order/');
					/*echo $e->getMessage();
					die;*/
					/*$this->messageManager->addError($e->getMessage());
					$resultRedirect->setUrl('sales/order/');
					return $resultRedirect;*/
				}

            //$resultRedirect->setPath('sales/*');
        } else {
            $resultRedirect->setPath('sales/order/');
        }
        return $resultRedirect;
    }
    
    /*protected $messageManager;

	   public function __construct(\Magento\Framework\Message\ManagerInterface $messageManager)
	   {
	       $this->messageManager = $messageManager;
	   }
	   try{
				$this->_getOrderCreateModel()->initFromOrder($order);
			}
			catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
				$resultRedirect->setUrl('sales/order/');
				return $resultRedirect;
			}*/
}
