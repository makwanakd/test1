<?php
 
namespace Ironedge\CompleteOrders\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Masspendingpayment extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
	 
	private $invoiceService;
	protected $shipmentFactory;
	protected $shipmentLoader;
	
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
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
			
				$order->setStatus('pending_payment');
	            $order->setState('pending_payment');
	            $order->save();
				$countCompleteOrder++;
        }
        $countNonCompleteOrder = $collection->count() - $countCompleteOrder;

        /*if ($countNonCompleteOrder && $countCompleteOrder) {
            $this->messageManager->addError('Order status changed to Pending payment');
        } elseif ($countNonCompleteOrder) {
            $this->messageManager->addError('Order status changed to Pending payment');
        }*/

        if ($countCompleteOrder) {
            $this->messageManager->addSuccess('Order status changed to Pending payment');
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
}