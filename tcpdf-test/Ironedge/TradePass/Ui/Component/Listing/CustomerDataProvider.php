<?php

namespace Ironedge\TradePass\Ui\Component\Listing;

class CustomerDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
   protected function _initSelect()
   {
      parent::_initSelect();
      $this->getSelect()->joinLeft(
        ['secondTable' => $this->getTable('ironedge_tradepass')],
        'main_table.entity_id = secondTable.customer_id',
        ['moreinfo']
      );
      return $this;
  }
}
