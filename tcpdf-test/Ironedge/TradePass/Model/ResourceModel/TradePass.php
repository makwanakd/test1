<?php

namespace Ironedge\TradePass\Model\ResourceModel;

Class TradePass extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected $_idFieldName = 'tradepass_id';

    /**
     * Initialize resource model.
     */
    protected function _construct() {
        $this->_init('ironedge_tradepass', 'tradepass_id');
    }

}
