<?php

namespace Ironedge\TradePass\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Framework\Mail\Message;
use Magento\Framework\App\Filesystem\DirectoryList;

class Invoiceattachment implements ObserverInterface {

    protected $_objectManager;
	protected $_transportBuilder;
	protected $messageManager;
	protected $message;
	protected $orderFactory;
	protected $_fileFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var OrderSender
     */
    protected $orderSender;
	
    public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
    	\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		ManagerInterface $messageManager,
		Message $message,
		Logger $logger,
		OrderSender $orderSender,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_transportBuilder = $transportBuilder;
		$this->messageManager = $messageManager;
		$this->message = $message;
        $this->logger = $logger;
        $this->orderSender = $orderSender;
		$this->orderFactory = $orderFactory;
		$this->_fileFactory = $fileFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) 
	{
		$order = $observer->getEvent()->getOrder();
	    if ($order instanceof \Magento\Framework\Model\AbstractModel) {
	       if($order->getState() == 'complete') {
		   $fullOrder = $this->orderFactory->create()->load($order->getId());
		   $_invoices = $fullOrder->getInvoiceCollection();
		   $invoiceId = FALSE;
			if($_invoices){
				foreach($_invoices as $invoice){
					$invoiceId = $invoice->getId();
				}
			}
		   if ($invoiceId) {
	            $invoice = $this->_objectManager->create('Magento\Sales\Api\InvoiceRepositoryInterface')->get($invoiceId);
	            if ($invoice) {
	                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf([$invoice]);
	                $date = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->date('Y-m-d_H-i-s');					
					$this->message->createAttachment(
			            $pdf->render(),
			            'application/pdf',
			            \Zend_Mime::DISPOSITION_ATTACHMENT,
			            \Zend_Mime::ENCODING_BASE64,
			            'invoice.pdf'
			        );
				   	$this->orderSender->send($order);
	            }
	        }
	       }
	    }
	    return $this;
	 }
}
