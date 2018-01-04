<?php

namespace Ironedge\CompleteOrders\Controller\Adminhtml\Order;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface as Logger;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Framework\Mail\Message;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\Service\InvoiceService;

error_reporting(E_ALL);
ini_set('display_errors', 1);
class Processingstate extends \Magento\Framework\App\Action\Action
{
     
	protected $_transportBuilder;
	protected $message;
	protected $orderFactory;
	protected $_fileFactory;
	private $invoiceService;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var OrderSender
     */
    protected $orderSender;
	
    public function __construct(
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		Message $message,
		Logger $logger,
		OrderSender $orderSender,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
		InvoiceService $invoiceService,
		\Magento\Framework\App\Action\Context $context
    ) {
        $this->_transportBuilder = $transportBuilder;
		$this->message = $message;
        $this->logger = $logger;
        $this->orderSender = $orderSender;
		$this->orderFactory = $orderFactory;
		$this->_fileFactory = $fileFactory;
		$this->invoiceService = $invoiceService;
		parent::__construct($context);
    }
    
    public function execute()
    {
		 $orderId = $this->getRequest()->getParam('order_id', null);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (empty($orderId)) {
            $this->messageManager->addError(__('We can\'t find the Order.'));

            return $resultRedirect->setPath('sales/order/view/', ['order_id' => $orderId]);
        }

	        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
			$order->setStatus('processing');
	        $order->setState('processing');
	        $order->save();
		
        if (!$order) {
            $this->messageManager->addError(__('We can\'t find the Order.'));

            return $resultRedirect->setPath('sales/order/view/', ['order_id' => $orderId]);
        }else{
			$this->messageManager->addSuccess('Order status changed to Processing');
		}

        try {
           	
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
        }

        return $resultRedirect->setPath('sales/order/view/', ['order_id' => $orderId]);
    }
}
