<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ironedge\TradePass\Block\Info;

class Purchaseorder extends \Magento\OfflinePayments\Block\Info\Purchaseorder
{
    /**
     * @var string
     */
    protected $_template = 'Ironedge_TradePass::info/purchaseorder.phtml';

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Ironedge_TradePass::info/pdf/purchaseorder.phtml');
        return $this->toHtml();
    }
}
