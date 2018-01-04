<?php

namespace Ironedge\TradePass\Model;

Class TradePass extends \Magento\Framework\Model\AbstractModel {

    /**
     * Initialize resource model.
     */
    protected function _construct() {
        $this->_init('Ironedge\TradePass\Model\ResourceModel\TradePass');
    }

}
