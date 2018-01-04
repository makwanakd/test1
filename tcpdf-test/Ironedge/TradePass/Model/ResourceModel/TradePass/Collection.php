<?php

namespace Ironedge\TradePass\Model\ResourceModel\TradePass;

Class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected $_idFieldName = 'tradepass_id';

    /**
     * Initialize resource collection.
     */
    protected function _construct() {
        $this->_init('Ironedge\TradePass\Model\TradePass', 'Ironedge\TradePass\Model\ResourceModel\TradePass');
    }

}
