<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Ironedge\CompleteOrders\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader;

class MassComplete extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
	 
	private $invoiceService;
	protected $shipmentFactory;
	protected $shipmentLoader;
	
    public function __construct(Context $context, \Magento\Sales\Model\Order $orderModel, Filter $filter, OrderInterface $order, CollectionFactory $collectionFactory, ShipmentFactory $shipmentFactory, InvoiceService $invoiceService, \Magento\Framework\Registry $registry, ShipmentLoader $shipmentLoader)
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->order = $order;
		$this->shipmentFactory = $shipmentFactory;
        $this->invoiceService = $invoiceService;
        $this->shipmentLoader = $shipmentLoader;
		$this->orderModel = $orderModel;
		$this->_registry = $registry;
    }

    /**
     * Cancel selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countCompleteOrder = 0;
        foreach ($collection->getItems() as $item) {
			$order = $this->_objectManager->create('Magento\Sales\Model\Order')
    			->load($item->getId());
			//echo "<pre>"; print_r($order->getData()); die;
			/*generate invoice and shipment start*/
			$invoiceArray = array();
			$invoiceArray['invoice']['do_shipment'] = 1; 
			$invoiceArray['invoice']['comment_text'] = "";
			foreach ($order->getAllVisibleItems() as $item) {
				$invoiceArray['invoice']['items'][$item->getId()] = (string)(int)$item->getQtyOrdered(); 
			}
			$order_id = $order->getId();
			
			if($order->getState()!='closed' && $order->getState()!='complete' && $order->getState()!='canceled'){
				$this->createInvoice($invoiceArray,$order_id);
				/*$order->setStatus('complete');
	            $order->setState('complete');
	            $order->save();*/
				$countCompleteOrder++;
				
			}else{
				$this->messageManager->addNotice(__('Few of selected orders are already completed, canceled or closed.'));
			}		
			/*generate invoice and shipment end*/
        }
        $countNonCompleteOrder = $collection->count() - $countCompleteOrder;

        if ($countNonCompleteOrder && $countCompleteOrder) {
            $this->messageManager->addError(__('%1 order(s) cannot be completed.', $countNonCompleteOrder));
        } elseif ($countNonCompleteOrder) {
            $this->messageManager->addError(__('You cannot complete the order(s).'));
        }

        if ($countCompleteOrder) {
            $this->messageManager->addSuccess(__('We completed %1 order(s).', $countCompleteOrder));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
    protected function _prepareShipment($invoice,$data)
    {
        $invoiceData = $data['invoice'];

        $shipment = $this->shipmentFactory->create(
            $invoice->getOrder(),
            isset($invoiceData['items']) ? $invoiceData['items'] : [],
            ''
        );
        return $shipment->register();
    }
    public function createInvoice($data,$orderId)
    {
    	$invoiceData = $data['invoice'];
        $invoiceItems = isset($invoiceData['items']) ? $invoiceData['items'] : [];
      
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
		
        $_invoices = $order->getInvoiceCollection();
	    $invoiceId = FALSE;
		if($_invoices){
			foreach($_invoices as $invoice){
				$invoiceId = $invoice->getId();
			}
		}
		$shipmentId = FALSE;
		if($order->canShip())
		{
			$_shipment_cols = $order->getShipmentsCollection();
			if($_shipment_cols){
				foreach($_shipment_cols as $_shipment_col){
					$shipmentId = $_shipment_col->getId();
				}
			}
		}
		
        if (!$invoiceId) {
          	$invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);  
          	/*if (!empty($data['capture_case'])) {
                $invoice->setRequestedCaptureCase($data['capture_case']);
            }*/
            $invoice->register();
            $invoice->getOrder()->setIsInProcess(true);
            
            $transactionSave = $this->_objectManager->create(
            'Magento\Framework\DB\Transaction'
	            )->addObject(
	                $invoice
	            )->addObject(
	                $invoice->getOrder()
	            );
	        if (!$shipmentId && $order->canShip()) {
				if(!$this->_registry->registry('current_shipment')){  
					$shipment = $this->_prepareShipment($invoice,$data);
		        }else{
					$shipment = $this->_registry->registry('current_shipment');		
				}
		        if ($shipment) {
		            $transactionSave->addObject($shipment);
		        }
	        }
	        $transactionSave->save();
        }else{
			if (!$shipmentId && $order->canShip()) {
	          	$this->createShipment($invoiceData, $orderId);  
	        }
		}
    }
	
    public function createShipment($invoiceData,$orderId)
    {
        $data = $invoiceData;

        /*$this->shipmentLoader->setOrderId($orderId);
        $this->shipmentLoader->setShipmentId(NULL);
        $this->shipmentLoader->setShipment($data);
        $this->shipmentLoader->setTracking(NULL);
        $shipment = $this->shipmentLoader->load(); 
		if(!$this->_registry->registry('current_shipment')){   
	        $shipment->register();
		}
		
        $this->_saveShipment($shipment);*/
		
		$order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
		 
		// Check if order can be shipped or has already shipped
		if (! $order->canShip()) {
			throw new \Magento\Framework\Exception\LocalizedException(
							__('You can\'t create an shipment.')
						);
		}
		 
		// Initialize the order shipment object
		$convertOrder = $this->_objectManager->create('Magento\Sales\Model\Convert\Order');
		$shipment = $convertOrder->toShipment($order);
		 
		// Loop through order items
		foreach ($order->getAllItems() AS $orderItem) {
			// Check if order item has qty to ship or is virtual
			if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
				continue;
			}
		 
			$qtyShipped = $orderItem->getQtyToShip();
		 
			// Create shipment item with qty
			$shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
		 
			// Add shipment item to shipment
			$shipment->addItem($shipmentItem);
		}
		 
		// Register shipment
		$shipment->register();
		 
		$shipment->getOrder()->setIsInProcess(true);
		 
		try {
			// Save created shipment and order
			$shipment->save();
			$shipment->getOrder()->save();
		 
			// Send email
			$this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
				->notify($shipment);
		 
			$shipment->save();
		} catch (\Exception $e) {
			throw new \Magento\Framework\Exception\LocalizedException(
							__($e->getMessage())
						);
		}
    }
	
    protected function _saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transaction = $this->_objectManager->create(
            'Magento\Framework\DB\Transaction'
        );
        $transaction->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();

        return $this;
    }
}
