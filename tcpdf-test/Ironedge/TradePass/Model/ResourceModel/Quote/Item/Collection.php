<?php

namespace Ironedge\TradePass\Model\ResourceModel\Quote\Item;

class Collection extends \Magento\Reports\Model\ResourceModel\Quote\Item\Collection
{
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $items = $this->getItems();
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item->getProductId();
        }
        $productData = $this->getProductData($productIds);
        $orderData = $this->getOrdersData($productIds);
        foreach ($items as $item) {
            $item->setId($item->getProductId());
            
            if(isset($productData[$item->getProductId()]['price'])){
				$item->setPrice($productData[$item->getProductId()]['price'] * $item->getBaseToGlobalRate());
			}else{
				$item->setPrice(0);
			}
			
			if(isset($productData[$item->getProductId()]['name'])){
				$item->setName($productData[$item->getProductId()]['name']);				
			}else{
				$item->setName('Gift card');
			}
			
            $item->setOrders(0);
            if (isset($orderData[$item->getProductId()])) {
                $item->setOrders($orderData[$item->getProductId()]['orders']);
            }
        }

        return $this;
    }
}
