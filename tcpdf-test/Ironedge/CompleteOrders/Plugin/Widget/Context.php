<?php

namespace Ironedge\CompleteOrders\Plugin\Widget;

class Context
{
    protected $_context;
    protected $_url;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Backend\Model\UrlInterface $url
    ) {
        $this->_context = $context;
        $this->_url = $url;
    }

    public function afterGetButtonList(
        \Magento\Backend\Block\Widget\Context $subject,
        $buttonList
    ) {
        $request = $this->_context->getRequest();

        if ($request->getFullActionName() == 'sales_order_view') {
            $buttonList->add(
                'quotation_send_order',
                [
                    'label' => __('Send quotation'),
                    'onclick' => 'setLocation(\'' . $this->getOrderQuotation($request) . '\')',
                    'class' => 'quotation'
                ],
                -1
            );
            $buttonList->add(
                'quotation_print_order',
                [
                    'label' => __('Preview quotation PDF'),
                    'onclick' => 'setLocation(\'' . $this->getOrderQuotationPreview($request) . '\')',
                    'class' => 'quotation'
                ],
                -1
            );
			$buttonList->add(
                'pending_payment_order',
                [
                    'label' => __('Pending Payment'),
                    'onclick' => 'setLocation(\'' . $this->getPendingPaymentUrl($request) . '\')',
                    'class' => 'quotation'
                ],
                -1
            );
			$buttonList->add(
                'processingstate_order',
                [
                    'label' => __('Processing'),
                    'onclick' => 'setLocation(\'' . $this->getProcessingStateUrl($request) . '\')',
                    'class' => 'quotation'
                ],
                -1
            );
        }

        return $buttonList;
    }

    public function getOrderQuotation($request)
    {
        $orderId = $request->getParam('order_id');

        return $this->_url->getUrl(
            'completeorders/order/quotation',
            [
                'order_id' => $orderId
            ]
        );
    }
    public function getOrderQuotationPreview($request)
    {
        $orderId = $request->getParam('order_id');

        return $this->_url->getUrl(
            'completeorders/order/quotationpreview',
            [
                'order_id' => $orderId
            ]
        );
    }
	public function getPendingPaymentUrl($request)
    {
        $orderId = $request->getParam('order_id');

        return $this->_url->getUrl(
            'completeorders/order/pendingpayment',
            [
                'order_id' => $orderId
            ]
        );
    }
	public function getProcessingStateUrl($request)
    {
        $orderId = $request->getParam('order_id');

        return $this->_url->getUrl(
            'completeorders/order/processingstate',
            [
                'order_id' => $orderId
            ]
        );
    }
}
