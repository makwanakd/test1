<?php

namespace Ironedge\CompleteOrders\Controller\Adminhtml\Order;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Framework\Mail\Message;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\Service\InvoiceService;

error_reporting(E_ALL);
ini_set('display_errors', 1);
class Quotationpreview extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
     protected $_objectManager;
	protected $_transportBuilder;
	protected $messageManager;
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
		\Magento\Framework\ObjectManagerInterface $objectManager,
    	\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		ManagerInterface $messageManager,
		Message $message,
		Logger $logger,
		OrderSender $orderSender,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
		InvoiceService $invoiceService,
		\Magento\Framework\App\Action\Context $context
    ) {
        $this->_objectManager = $objectManager;
        $this->_transportBuilder = $transportBuilder;
		$this->messageManager = $messageManager;
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
            $this->messageManager->addError(__('We can\'t find the Quotation.'));

            return $resultRedirect->setPath('sales/order/view/', ['order_id' => $orderId]);
        }

        $order = $this->_objectManager
            ->get('\Magento\Sales\Api\Data\OrderInterface')
            ->load($orderId);
		
		   $fullOrder = $this->orderFactory->create()->load($order->getId());
		   $_invoices = $fullOrder->getInvoiceCollection();
		   $invoiceId = FALSE;
			if($_invoices){
				foreach($_invoices as $invoice){
					$invoiceId = $invoice->getId();
				}
			}
		   if (true) {
		   		if($invoiceId){
					$invoice = $this->_objectManager->create('Magento\Sales\Api\InvoiceRepositoryInterface')->get($invoiceId);
				}else{
					foreach ($fullOrder->getAllVisibleItems() as $item) {
						$invoiceArray['invoice']['items'][$item->getId()] = (string)(int)$item->getQtyOrdered(); 
					}
					$invoiceData = $invoiceArray['invoice'];
					$invoiceItems = isset($invoiceData['items']) ? $invoiceData['items'] : [];
					$invoice = $this->invoiceService->prepareInvoice($fullOrder, $invoiceItems);
				}
				
				
	            if ($invoice) {
	                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Invoice')->getQuotationPdf([$invoice]);
	                return $this->_fileFactory->create(
	                    'Quotation.pdf',
	                    $pdf->render(),
	                    DirectoryList::VAR_DIR,
	                    'application/pdf'
	                );
				   	//$this->orderSender->send($order);
					
					//$this->messageManager->addSuccess(__('The Quotation has been sent.'));
	            }
	        }
        if (!$order) {
            $this->messageManager->addError(__('We can\'t find the Quotation.'));

            return $resultRedirect->setPath('sales/order/view/', ['order_id' => $orderId]);
        }

        try {
           	
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
        }

        return $resultRedirect->setPath('sales/order/view/', ['order_id' => $orderId]);
    }
}
